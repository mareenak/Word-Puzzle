<?php

namespace App\Controller;

use App\Entity\Puzzle;
use App\Entity\Submission;
use App\Form\SubmissionType;
use App\Service\WordPuzzleService;
use App\Service\DbLoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PuzzleController extends AbstractController
{
    private WordPuzzleService $puzzleService;
    private EntityManagerInterface $em;
    private DbLoggerService $logger;

    public function __construct(WordPuzzleService $puzzleService, EntityManagerInterface $em, DbLoggerService $logger)
    {
        $this->puzzleService = $puzzleService;
        $this->em = $em;
        $this->logger = $logger;
    }

    #[Route('/', name: 'home')]
    public function home(Request $request, SessionInterface $session): Response
    {
        try {
            if ($request->isMethod('POST')) {
                $name = $request->request->get('student_name');
                if ($name) {
                    $session->set('student_name', $name);
                    return $this->redirectToRoute('start_puzzle');
                }
            }

            return $this->render('puzzle/home.html.twig');
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Home page error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    #[Route('/start', name: 'start_puzzle')]
    public function start(SessionInterface $session): Response
    {
        try {
            $studentName = $session->get('student_name');

            if (!$studentName) {
                return $this->redirectToRoute('home');
            }

            $letters = $this->puzzleService->generateInitialLetters();

            $puzzle = new Puzzle();
            $puzzle->setStudentName($studentName);
            $puzzle->setInitialLetters($letters);
            $puzzle->setRemainingLetters($letters);
            $puzzle->setIsCompleted(false);
            $this->em->persist($puzzle);
            $this->em->flush();

            return $this->redirectToRoute('submit_word', ['id' => $puzzle->getId()]);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Start puzzle error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    #[Route('/submit/{id}', name: 'submit_word')]
    public function submit(Request $request, Puzzle $puzzle): Response
    {
        try {
            $submission = new Submission();
            $form = $this->createForm(SubmissionType::class, $submission);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $word = $submission->getWord();

                if (!$this->puzzleService->isValidWord($word)) {
                    $this->addFlash('error', 'Invalid English word.');
                } elseif (!$this->puzzleService->isWordPossible($word, $puzzle->getRemainingLetters())) {
                    $this->addFlash('error', 'Not enough letters available.');
                } else {
                    $submission->setScore($this->puzzleService->calculateScore($word));
                    $submission->setPuzzle($puzzle);
                    $this->em->persist($submission);

                    $newLetters = $this->puzzleService->updateRemainingLetters($puzzle->getRemainingLetters(), $word);
                    $puzzle->setRemainingLetters($newLetters);
                    $this->em->flush();

                    $this->addFlash('success', "Submitted '$word' for " . strlen($word) . ' points.');

                    return $this->redirectToRoute('submit_word', ['id' => $puzzle->getId()]);
                }
            }

            return $this->render('puzzle/submit.html.twig', [
                'puzzle' => $puzzle,
                'form' => $form->createView(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Submit word error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    #[Route('/end/{id}', name: 'end_game')]
    public function endGame(Puzzle $puzzle): Response
    {
        try {
            $puzzle->setIsCompleted(true);
            $this->em->flush();

            $possibleWords = $this->puzzleService->findAllPossibleWords($puzzle->getRemainingLetters());

            return $this->render('puzzle/result.html.twig', [
                'puzzle' => $puzzle,
                'possibleWords' => $possibleWords,
            ]);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'End game error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    #[Route('/leaderboard', name: 'leaderboard')]
    public function leaderboard(): Response
    {
        try {
            $qb = $this->em->createQueryBuilder();

            $qb->select('p.id AS puzzleId, p.studentName, p.initialLetters, SUM(s.score) AS totalScore')
                ->from(Submission::class, 's')
                ->join('s.puzzle', 'p')
                ->groupBy('p.id')
                ->orderBy('totalScore', 'DESC')
                ->setMaxResults(10);

            $entries = $qb->getQuery()->getResult();

            return $this->render('puzzle/leaderboard.html.twig', [
                'entries' => $entries,
            ]);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Leaderboard error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
    
    #[Route('/test-db-logger', name: 'test_db_logger')]
    public function testDbLogger(): Response
    {
        try {
            throw new \RuntimeException('Intentional test error');
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Test error caught', ['trace' => $e->getTraceAsString()]);
            throw $e; // still let Symfony show the error
        }
    }
}

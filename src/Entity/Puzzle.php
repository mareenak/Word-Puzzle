<?php

// src/Entity/Puzzle.php

namespace App\Entity;

use App\Repository\PuzzleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PuzzleRepository::class)]
class Puzzle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $initialLetters = null;

    #[ORM\Column(length: 255)]
    private ?string $remainingLetters = null;

    #[ORM\Column]
    private ?bool $isCompleted = null;

    #[ORM\Column(length: 255)]
    private ?string $studentName = null;

    #[ORM\OneToMany(mappedBy: 'puzzle', targetEntity: Submission::class, cascade: ['persist', 'remove'])]
    private Collection $submissions;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialLetters(): ?string
    {
        return $this->initialLetters;
    }

    public function setInitialLetters(string $initialLetters): static
    {
        $this->initialLetters = $initialLetters;
        return $this;
    }

    public function getRemainingLetters(): ?string
    {
        return $this->remainingLetters;
    }

    public function setRemainingLetters(string $remainingLetters): static
    {
        $this->remainingLetters = $remainingLetters;
        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    public function getStudentName(): ?string
    {
        return $this->studentName;
    }

    public function setStudentName(string $studentName): static
    {
        $this->studentName = $studentName;
        return $this;
    }

    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }
}


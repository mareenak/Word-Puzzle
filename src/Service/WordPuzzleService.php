<?php
// src/Service/WordPuzzleService.php

namespace App\Service;

use App\Service\DbLoggerService;
use GuzzleHttp\Client;

class WordPuzzleService
{
    protected array $dictionary = [];

    public function __construct(private readonly DbLoggerService $logger)
    {
        try {
            $path = __DIR__ . '/../../data/words.txt';
            if (!file_exists($path)) {
                throw new \RuntimeException("Dictionary file not found: $path");
            }

            $this->dictionary = file($path, FILE_IGNORE_NEW_LINES);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Failed to load dictionary: ' . $e->getMessage());
        }
    }

    public function generateInitialLetters(): string
    {
        try {
            $letters = '';
            $characters = 'abcdefghijklmnopqrstuvwxyz';
            for ($i = 0; $i < 14; $i++) {
                $letters .= $characters[random_int(0, 25)];
            }
            return $letters;
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Letter generation failed: ' . $e->getMessage());
            return '';
        }
    }

    public function isValidWord(string $word): bool
    {
        try {

            $client = new \GuzzleHttp\Client();
            $url = 'https://api.dictionaryapi.dev/api/v2/entries/en/' . urlencode($word);
            $response = @file_get_contents($url);
            return $response;
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Validation check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function isWordPossible(string $word, string $remainingLetters): bool
    {
        try {
            $wordLetters = count_chars(strtolower($word), 1);
            $availableLetters = count_chars(strtolower($remainingLetters), 1);

            foreach ($wordLetters as $char => $count) {
                if (!isset($availableLetters[$char]) || $availableLetters[$char] < $count) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Word possibility check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function calculateScore(string $word): int
    {
        try {
            return strlen($word); // Simple scoring
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Score calculation failed: ' . $e->getMessage());
            return 0;
        }
    }

    public function updateRemainingLetters(string $letters, string $usedWord): string
    {
        try {
            $lettersArray = str_split($letters);
            foreach (str_split($usedWord) as $char) {
                $pos = array_search($char, $lettersArray);
                if ($pos !== false) {
                    unset($lettersArray[$pos]);
                }
            }

            return implode('', $lettersArray);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Updating letters failed: ' . $e->getMessage());
            return $letters;
        }
    }

    public function findAllPossibleWords(string $letters): array
    {
        try {
            $availableCount = count_chars($letters, 1);
            $possibleWords = [];

            foreach ($this->dictionary as $word) {
                $word = strtolower(trim($word));
                $wordCount = count_chars($word, 1);

                $isPossible = true;
                foreach ($wordCount as $char => $count) {
                    if (!isset($availableCount[$char]) || $availableCount[$char] < $count) {
                        $isPossible = false;
                        break;
                    }
                }

                if ($isPossible) {
                    $possibleWords[] = $word;
                }
            }

            usort($possibleWords, fn($a, $b) => strlen($b) <=> strlen($a));
            return array_slice($possibleWords, 0, 10);
        } catch (\Throwable $e) {
            $this->logger->log('error', 'Finding words failed: ' . $e->getMessage());
            return [];
        }
    }
}

<?php
// tests/Service/WordPuzzleServiceTest.php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\WordPuzzleService;
use App\Service\DbLoggerService;
use PHPUnit\Framework\MockObject\MockObject;

class WordPuzzleServiceTest extends TestCase
{
    private WordPuzzleService $service;

    protected function setUp(): void
    {
        /** @var DbLoggerService&MockObject $mockLogger */
        $mockLogger = $this->createMock(DbLoggerService::class);

        $this->service = new WordPuzzleService($mockLogger);
    }

    public function testGenerateInitialLettersLength()
    {
        $letters = $this->service->generateInitialLetters();
        $this->assertEquals(14, strlen($letters));
    }

    public function testIsValidWord()
    {
        $this->assertTrue($this->service->isValidWord('apple'));
        $this->assertFalse($this->service->isValidWord('invalidwordzz'));
    }

    public function testIsWordPossible()
    {
        $this->assertTrue($this->service->isWordPossible('cat', 'taccc'));
        $this->assertFalse($this->service->isWordPossible('dog', 'gggg'));
    }

    public function testCalculateScore()
    {
        $this->assertEquals(5, $this->service->calculateScore('hello'));
    }

    public function testUpdateRemainingLetters()
    {
        $updated = $this->service->updateRemainingLetters('abcdef', 'bed');
        $this->assertEquals('acf', $updated);
    }
}

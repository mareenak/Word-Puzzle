<?php
// src/Service/DbLoggerService.php

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class DbLoggerService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function log(string $level, string $message, array $context = []): void
    {
        try {
            $entry = new Log();
            $entry->setLevel($level);
            $entry->setMessage($message);
            $entry->setContext($context);
            $entry->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($entry);
            $this->em->flush();
        } catch (\Throwable $e) {
            error_log("Failed to log to database: " . $e->getMessage());
        }
    }
}

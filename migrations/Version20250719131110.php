<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719131110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leaderboard (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, score INT NOT NULL, UNIQUE INDEX UNIQ_182E5253C3F17511 (word), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_submission (id INT AUTO_INCREMENT NOT NULL, puzzle_id INT NOT NULL, student_name VARCHAR(255) NOT NULL, used_letters JSON NOT NULL, words JSON NOT NULL, score INT NOT NULL, completed TINYINT(1) NOT NULL, INDEX IDX_36DAB712D9816812 (puzzle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_submission ADD CONSTRAINT FK_36DAB712D9816812 FOREIGN KEY (puzzle_id) REFERENCES puzzle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_submission DROP FOREIGN KEY FK_36DAB712D9816812');
        $this->addSql('DROP TABLE leaderboard');
        $this->addSql('DROP TABLE student_submission');
    }
}

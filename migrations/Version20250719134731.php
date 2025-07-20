<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719134731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leaderboard_entry (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, score VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puzzle (id INT AUTO_INCREMENT NOT NULL, initial_letters VARCHAR(255) NOT NULL, remaining_letters VARCHAR(255) NOT NULL, is_completed TINYINT(1) NOT NULL, student_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, puzzle_id INT NOT NULL, word VARCHAR(255) NOT NULL, score INT NOT NULL, INDEX IDX_DB055AF3D9816812 (puzzle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3D9816812 FOREIGN KEY (puzzle_id) REFERENCES puzzle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3D9816812');
        $this->addSql('DROP TABLE leaderboard_entry');
        $this->addSql('DROP TABLE puzzle');
        $this->addSql('DROP TABLE submission');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129212608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_notes_name_description');
        $this->addSql('CREATE INDEX idx_notes_name ON notes (name)');
        $this->addSql('CREATE INDEX idx_notes_description ON notes (description)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_notes_name');
        $this->addSql('DROP INDEX idx_notes_description');
        $this->addSql('CREATE INDEX idx_notes_name_description ON "notes" (name, description)');
    }
}

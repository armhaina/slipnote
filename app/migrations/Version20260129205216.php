<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260129205216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes ADD is_trashed BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE notes DROP is_trash');
        $this->addSql('COMMENT ON COLUMN notes.is_trashed IS \'Удалено\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "notes" ADD is_trash BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE "notes" DROP is_trashed');
        $this->addSql('COMMENT ON COLUMN "notes".is_trash IS \'Корзина\'');
    }
}

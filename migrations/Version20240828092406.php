<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828092406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366712520F3');
        $this->addSql('DROP INDEX UNIQ_7E1A4366712520F3 ON trade');
        $this->addSql('ALTER TABLE trade DROP wallet_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade ADD wallet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E1A4366712520F3 ON trade (wallet_id)');
    }
}

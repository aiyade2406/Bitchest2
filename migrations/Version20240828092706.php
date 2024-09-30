<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828092706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade ADD crypto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366E9571A63 FOREIGN KEY (crypto_id) REFERENCES crypto (id)');
        $this->addSql('CREATE INDEX IDX_7E1A4366E9571A63 ON trade (crypto_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366E9571A63');
        $this->addSql('DROP INDEX IDX_7E1A4366E9571A63 ON trade');
        $this->addSql('ALTER TABLE trade DROP crypto_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229100921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesas ADD zonas_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mesas ADD CONSTRAINT FK_4825EFB9536A4DBA FOREIGN KEY (zonas_id) REFERENCES zonas (id)');
        $this->addSql('CREATE INDEX IDX_4825EFB9536A4DBA ON mesas (zonas_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesas DROP FOREIGN KEY FK_4825EFB9536A4DBA');
        $this->addSql('DROP INDEX IDX_4825EFB9536A4DBA ON mesas');
        $this->addSql('ALTER TABLE mesas DROP zonas_id');
    }
}

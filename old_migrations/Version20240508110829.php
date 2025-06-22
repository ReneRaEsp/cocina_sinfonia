<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508110829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD producttienda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660FCCA42B1 FOREIGN KEY (producttienda_id) REFERENCES productostienda (id)');
        $this->addSql('CREATE INDEX IDX_4B365660FCCA42B1 ON stock (producttienda_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660FCCA42B1');
        $this->addSql('DROP INDEX IDX_4B365660FCCA42B1 ON stock');
        $this->addSql('ALTER TABLE stock DROP producttienda_id');
    }
}

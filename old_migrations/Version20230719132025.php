<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719132025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD comida_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_4B365660399E35A6 ON stock (comida_id)');
    }

    public function down(Schema $schema): void
    {
    }
}

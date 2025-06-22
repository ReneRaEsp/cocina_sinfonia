<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611113056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statscomensales (id INT AUTO_INCREMENT NOT NULL, mesa_id INT DEFAULT NULL, fecha DATETIME DEFAULT NULL, num_comensales INT NOT NULL, INDEX IDX_14EFCC938BDC7AE9 (mesa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statscomensales ADD CONSTRAINT FK_14EFCC938BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statscomensales DROP FOREIGN KEY FK_14EFCC938BDC7AE9');
        $this->addSql('DROP TABLE statscomensales');
    }
}

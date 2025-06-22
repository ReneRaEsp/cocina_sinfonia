<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216094700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pedidos (id INT AUTO_INCREMENT NOT NULL, mesa_id INT NOT NULL, comida_id INT NOT NULL, INDEX IDX_6716CCAA8BDC7AE9 (mesa_id), INDEX IDX_6716CCAA399E35A6 (comida_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA8BDC7AE9');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA399E35A6');
        $this->addSql('DROP TABLE pedidos');
    }
}

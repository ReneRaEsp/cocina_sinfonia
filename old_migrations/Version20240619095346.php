<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619095346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE impuesto_facturas (id INT AUTO_INCREMENT NOT NULL, factura_id INT DEFAULT NULL, impuesto INT DEFAULT NULL, cantidad DOUBLE PRECISION NOT NULL, INDEX IDX_7DA6330F04F795F (factura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE impuesto_facturas ADD CONSTRAINT FK_7DA6330F04F795F FOREIGN KEY (factura_id) REFERENCES facturas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE impuesto_facturas DROP FOREIGN KEY FK_7DA6330F04F795F');
        $this->addSql('DROP TABLE impuesto_facturas');
    }
}

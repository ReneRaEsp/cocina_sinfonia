<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426071750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historial_pedidos (id INT AUTO_INCREMENT NOT NULL, mesa INT DEFAULT NULL, comida VARCHAR(200) DEFAULT NULL, comentarios VARCHAR(255) DEFAULT NULL, extras LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', invitacion TINYINT(1) DEFAULT NULL, descuento DOUBLE PRECISION DEFAULT NULL, descuento_eur DOUBLE PRECISION DEFAULT NULL, num_ref VARCHAR(25) DEFAULT NULL, precio DOUBLE PRECISION DEFAULT NULL, precio_total DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE papelera ADD pedidos LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD num_ref VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ventas ADD pedidos_ref LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE historial_pedidos');
        $this->addSql('ALTER TABLE papelera DROP pedidos');
        $this->addSql('ALTER TABLE pedidos DROP num_ref');
        $this->addSql('ALTER TABLE ventas DROP pedidos_ref');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418061957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historial_pedidos (id INT AUTO_INCREMENT NOT NULL, mesa INT DEFAULT NULL, comida VARCHAR(200) DEFAULT NULL, comentarios VARCHAR(255) DEFAULT NULL, extras LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', invitacion TINYINT(1) DEFAULT NULL, descuento DOUBLE PRECISION NOT NULL, descuento_eur DOUBLE PRECISION NOT NULL, num_ref VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ventas ADD CONSTRAINT FK_808D9E8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE historial_pedidos');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
    }
}

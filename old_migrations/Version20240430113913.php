<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430113913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE productostienda (id INT AUTO_INCREMENT NOT NULL, id_producto INT DEFAULT NULL, nombre VARCHAR(255) DEFAULT NULL, hs_intrstat VARCHAR(255) DEFAULT NULL, pais_origen VARCHAR(255) DEFAULT NULL, pais_origen_2 VARCHAR(255) DEFAULT NULL, prov_preferente VARCHAR(255) DEFAULT NULL, precio_coste DOUBLE PRECISION DEFAULT NULL, precio_venta_bruto DOUBLE PRECISION DEFAULT NULL, iva INT DEFAULT NULL, pvp DOUBLE PRECISION DEFAULT NULL, stock INT DEFAULT NULL, unidad_medida VARCHAR(255) DEFAULT NULL, id_gesio VARCHAR(255) DEFAULT NULL, referencia VARCHAR(255) DEFAULT NULL, ref_prov_preferente VARCHAR(255) DEFAULT NULL, marca VARCHAR(255) DEFAULT NULL, descripcion VARCHAR(255) DEFAULT NULL, descripcion_web VARCHAR(255) DEFAULT NULL, descripcion_tecnica VARCHAR(255) DEFAULT NULL, peso DOUBLE PRECISION DEFAULT NULL, dim_x DOUBLE PRECISION DEFAULT NULL, dim_y DOUBLE PRECISION DEFAULT NULL, dim_z DOUBLE PRECISION DEFAULT NULL, codebar VARCHAR(255) DEFAULT NULL, tipo VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, subcuenta VARCHAR(255) DEFAULT NULL, descripcion_espanol VARCHAR(255) DEFAULT NULL, descripcion_web_espanol VARCHAR(255) DEFAULT NULL, familia VARCHAR(255) DEFAULT NULL, familia_ref VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE productostienda');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
    }
}

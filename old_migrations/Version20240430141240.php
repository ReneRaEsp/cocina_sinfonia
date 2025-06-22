<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430141240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE productostienda DROP id_producto, DROP hs_intrstat, DROP pais_origen, DROP pais_origen_2, DROP prov_preferente, DROP precio_coste, DROP precio_venta_bruto, DROP iva, DROP stock, DROP unidad_medida, DROP id_gesio, DROP referencia, DROP ref_prov_preferente, DROP marca, DROP descripcion, DROP descripcion_web, DROP descripcion_tecnica, DROP peso, DROP dim_x, DROP dim_y, DROP dim_z, DROP codebar, DROP tipo, DROP url, DROP subcuenta, DROP descripcion_espanol, DROP descripcion_web_espanol, DROP familia, DROP familia_ref');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE productostienda ADD id_producto INT DEFAULT NULL, ADD hs_intrstat VARCHAR(255) DEFAULT NULL, ADD pais_origen VARCHAR(255) DEFAULT NULL, ADD pais_origen_2 VARCHAR(255) DEFAULT NULL, ADD prov_preferente VARCHAR(255) DEFAULT NULL, ADD precio_coste DOUBLE PRECISION DEFAULT NULL, ADD precio_venta_bruto DOUBLE PRECISION DEFAULT NULL, ADD iva INT DEFAULT NULL, ADD stock INT DEFAULT NULL, ADD unidad_medida VARCHAR(255) DEFAULT NULL, ADD id_gesio VARCHAR(255) DEFAULT NULL, ADD referencia VARCHAR(255) DEFAULT NULL, ADD ref_prov_preferente VARCHAR(255) DEFAULT NULL, ADD marca VARCHAR(255) DEFAULT NULL, ADD descripcion VARCHAR(255) DEFAULT NULL, ADD descripcion_web VARCHAR(255) DEFAULT NULL, ADD descripcion_tecnica VARCHAR(255) DEFAULT NULL, ADD peso DOUBLE PRECISION DEFAULT NULL, ADD dim_x DOUBLE PRECISION DEFAULT NULL, ADD dim_y DOUBLE PRECISION DEFAULT NULL, ADD dim_z DOUBLE PRECISION DEFAULT NULL, ADD codebar VARCHAR(255) DEFAULT NULL, ADD tipo VARCHAR(255) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD subcuenta VARCHAR(255) DEFAULT NULL, ADD descripcion_espanol VARCHAR(255) DEFAULT NULL, ADD descripcion_web_espanol VARCHAR(255) DEFAULT NULL, ADD familia VARCHAR(255) DEFAULT NULL, ADD familia_ref VARCHAR(255) DEFAULT NULL');
    }
}

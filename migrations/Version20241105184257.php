<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105184257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auditoria (id INT AUTO_INCREMENT NOT NULL, usuario VARCHAR(255) NOT NULL, modificado VARCHAR(255) NOT NULL, fecha DATE NOT NULL, rol_anterior VARCHAR(255) NOT NULL, rol_nuevo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cajaregistro (id INT AUTO_INCREMENT NOT NULL, dia DATETIME NOT NULL, iniciocaja DOUBLE PRECISION DEFAULT NULL, finalcaja DOUBLE PRECISION DEFAULT NULL, totalcaja DOUBLE PRECISION DEFAULT NULL, descuadre DOUBLE PRECISION DEFAULT NULL, observaciones VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comida (id INT AUTO_INCREMENT NOT NULL, type_food_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(200) DEFAULT NULL, precio NUMERIC(10, 2) NOT NULL, unitario TINYINT(1) DEFAULT NULL, iscomida TINYINT(1) DEFAULT NULL, isbebida TINYINT(1) DEFAULT NULL, extra TINYINT(1) DEFAULT NULL, posiblesextras LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', num_plato INT DEFAULT NULL, numplato INT DEFAULT NULL, rutaimg VARCHAR(250) DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_91AE1CD792AFE831 (type_food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facturas (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, concepto VARCHAR(100) NOT NULL, fecha_factura DATETIME NOT NULL, empresa VARCHAR(50) NOT NULL, importe NUMERIC(10, 2) DEFAULT NULL, ruta_pdf TINYTEXT NOT NULL, tipo VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichaje (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(255) NOT NULL, fecha DATE NOT NULL, inicio_am TIME NOT NULL, fin_am TIME NOT NULL, inicio_pm TIME NOT NULL, fin_pm TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historial_pedidos (id INT AUTO_INCREMENT NOT NULL, mesa VARCHAR(255) DEFAULT NULL, comida VARCHAR(200) DEFAULT NULL, comentarios VARCHAR(255) DEFAULT NULL, extras LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', invitacion TINYINT(1) DEFAULT NULL, descuento DOUBLE PRECISION DEFAULT NULL, descuento_eur DOUBLE PRECISION DEFAULT NULL, num_ref VARCHAR(25) DEFAULT NULL, precio DOUBLE PRECISION DEFAULT NULL, precio_total DOUBLE PRECISION DEFAULT NULL, fecha DATETIME NOT NULL, comensales INT DEFAULT NULL, user VARCHAR(25) DEFAULT NULL, iva INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE impresoras (id INT AUTO_INCREMENT NOT NULL, sn_cocina VARCHAR(255) DEFAULT NULL, sn_barra VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE impuesto_facturas (id INT AUTO_INCREMENT NOT NULL, factura_id INT DEFAULT NULL, impuesto INT DEFAULT NULL, cantidad DOUBLE PRECISION NOT NULL, INDEX IDX_7DA6330F04F795F (factura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, dir VARCHAR(255) NOT NULL, telf VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, url VARCHAR(50) DEFAULT NULL, cif VARCHAR(50) NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesas (id INT AUTO_INCREMENT NOT NULL, zonas_id INT DEFAULT NULL, numero VARCHAR(255) NOT NULL, pagado NUMERIC(10, 2) DEFAULT NULL, por_pagar NUMERIC(10, 2) NOT NULL, union_mesas LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', localizacion VARCHAR(50) NOT NULL, comensales INT DEFAULT NULL, factura TINYINT(1) DEFAULT NULL, coord_x DOUBLE PRECISION DEFAULT NULL, coord_y DOUBLE PRECISION DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, INDEX IDX_4825EFB9536A4DBA (zonas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE papelera (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(255) NOT NULL, pagado DOUBLE PRECISION NOT NULL, por_pagar DOUBLE PRECISION NOT NULL, union_mesas LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', localizacion VARCHAR(20) NOT NULL, zonas_id INT NOT NULL, comensales INT NOT NULL, factura INT DEFAULT NULL, pedidos LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedidos (id INT AUTO_INCREMENT NOT NULL, mesa_id INT NOT NULL, comida_id INT DEFAULT NULL, producttienda_id INT DEFAULT NULL, comentarios VARCHAR(255) DEFAULT NULL, marchando TINYINT(1) DEFAULT NULL, extras LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', invitacion TINYINT(1) DEFAULT NULL, descuento DOUBLE PRECISION DEFAULT NULL, descuento_eur DOUBLE PRECISION DEFAULT NULL, estado INT DEFAULT NULL, num_plato INT DEFAULT NULL, num_ref VARCHAR(255) DEFAULT NULL, entregado TINYINT(1) DEFAULT NULL, INDEX IDX_6716CCAA8BDC7AE9 (mesa_id), INDEX IDX_6716CCAA399E35A6 (comida_id), INDEX IDX_6716CCAAFCCA42B1 (producttienda_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personalizacion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE productostienda (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, pvp DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proveedores (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, dir VARCHAR(70) NOT NULL, email VARCHAR(50) NOT NULL, telf INT NOT NULL, nif VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registro_usuarios (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, fecha DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservas (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, nombre VARCHAR(50) NOT NULL, telefono INT NOT NULL, comensales INT NOT NULL, tipo VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statscomensales (id INT AUTO_INCREMENT NOT NULL, mesa_id INT DEFAULT NULL, fecha DATETIME DEFAULT NULL, num_comensales INT NOT NULL, INDEX IDX_14EFCC938BDC7AE9 (mesa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statscomida (id INT AUTO_INCREMENT NOT NULL, comida_id INT DEFAULT NULL, tipocomida_id INT DEFAULT NULL, mesa_id INT DEFAULT NULL, tienda_id INT DEFAULT NULL, fecha DATETIME NOT NULL, INDEX IDX_6D0BDDB3399E35A6 (comida_id), INDEX IDX_6D0BDDB3C19B2A0F (tipocomida_id), INDEX IDX_6D0BDDB38BDC7AE9 (mesa_id), INDEX IDX_6D0BDDB319BA6D46 (tienda_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, type_food_id INT DEFAULT NULL, comida_id INT DEFAULT NULL, producttienda_id INT DEFAULT NULL, name VARCHAR(55) NOT NULL, description VARCHAR(255) DEFAULT NULL, amount INT NOT NULL, INDEX IDX_4B36566092AFE831 (type_food_id), INDEX IDX_4B365660399E35A6 (comida_id), INDEX IDX_4B365660FCCA42B1 (producttienda_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sunmi_cloud_printer (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, mesaid_id INT NOT NULL, numeroticket INT NOT NULL, pedidos LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_54469DF47460A381 (mesaid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickettofactura (id INT AUTO_INCREMENT NOT NULL, ref VARCHAR(255) NOT NULL, ruta VARCHAR(255) NOT NULL, fecha DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tipo_comida (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, icon VARCHAR(50) DEFAULT NULL, active TINYINT(1) NOT NULL, ruta_img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ventas (id INT AUTO_INCREMENT NOT NULL, mesa_id INT DEFAULT NULL, fecha DATETIME NOT NULL, pagado NUMERIC(10, 2) DEFAULT NULL, pago VARCHAR(50) NOT NULL, num_mesa VARCHAR(255) DEFAULT NULL, comesales INT DEFAULT NULL, ref VARCHAR(255) DEFAULT NULL, num_ticket VARCHAR(255) DEFAULT NULL, pedidos_ref LONGTEXT DEFAULT NULL, observaciones VARCHAR(400) DEFAULT NULL, iva INT NOT NULL, importe_iva DOUBLE PRECISION NOT NULL, INDEX IDX_808D9E8BDC7AE9 (mesa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zonas (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comida ADD CONSTRAINT FK_91AE1CD792AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE impuesto_facturas ADD CONSTRAINT FK_7DA6330F04F795F FOREIGN KEY (factura_id) REFERENCES facturas (id)');
        $this->addSql('ALTER TABLE mesas ADD CONSTRAINT FK_4825EFB9536A4DBA FOREIGN KEY (zonas_id) REFERENCES zonas (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAFCCA42B1 FOREIGN KEY (producttienda_id) REFERENCES productostienda (id)');
        $this->addSql('ALTER TABLE statscomensales ADD CONSTRAINT FK_14EFCC938BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB3399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB3C19B2A0F FOREIGN KEY (tipocomida_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB38BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB319BA6D46 FOREIGN KEY (tienda_id) REFERENCES productostienda (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566092AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660FCCA42B1 FOREIGN KEY (producttienda_id) REFERENCES productostienda (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF47460A381 FOREIGN KEY (mesaid_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE ventas ADD CONSTRAINT FK_808D9E8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comida DROP FOREIGN KEY FK_91AE1CD792AFE831');
        $this->addSql('ALTER TABLE impuesto_facturas DROP FOREIGN KEY FK_7DA6330F04F795F');
        $this->addSql('ALTER TABLE mesas DROP FOREIGN KEY FK_4825EFB9536A4DBA');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA8BDC7AE9');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA399E35A6');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAFCCA42B1');
        $this->addSql('ALTER TABLE statscomensales DROP FOREIGN KEY FK_14EFCC938BDC7AE9');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB3399E35A6');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB3C19B2A0F');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB38BDC7AE9');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB319BA6D46');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566092AFE831');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660399E35A6');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660FCCA42B1');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF47460A381');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
        $this->addSql('DROP TABLE auditoria');
        $this->addSql('DROP TABLE cajaregistro');
        $this->addSql('DROP TABLE comida');
        $this->addSql('DROP TABLE facturas');
        $this->addSql('DROP TABLE fichaje');
        $this->addSql('DROP TABLE historial_pedidos');
        $this->addSql('DROP TABLE impresoras');
        $this->addSql('DROP TABLE impuesto_facturas');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE mesas');
        $this->addSql('DROP TABLE papelera');
        $this->addSql('DROP TABLE pedidos');
        $this->addSql('DROP TABLE personalizacion');
        $this->addSql('DROP TABLE productostienda');
        $this->addSql('DROP TABLE proveedores');
        $this->addSql('DROP TABLE registro_usuarios');
        $this->addSql('DROP TABLE reservas');
        $this->addSql('DROP TABLE statscomensales');
        $this->addSql('DROP TABLE statscomida');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE sunmi_cloud_printer');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE tickettofactura');
        $this->addSql('DROP TABLE tipo_comida');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE ventas');
        $this->addSql('DROP TABLE zonas');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

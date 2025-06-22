<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521103916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comida (id INT AUTO_INCREMENT NOT NULL, type_food_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(200) DEFAULT NULL, precio NUMERIC(10, 2) NOT NULL, INDEX IDX_91AE1CD792AFE831 (type_food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facturas (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, concepto VARCHAR(100) NOT NULL, fecha_factura DATETIME NOT NULL, empresa VARCHAR(50) NOT NULL, importe NUMERIC(10, 2) DEFAULT NULL, ruta_pdf TINYTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesas (id INT AUTO_INCREMENT NOT NULL, numero INT NOT NULL, pagado NUMERIC(10, 2) DEFAULT NULL, por_pagar NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedidos (id INT AUTO_INCREMENT NOT NULL, mesa_id INT NOT NULL, comida_id INT NOT NULL, INDEX IDX_6716CCAA8BDC7AE9 (mesa_id), INDEX IDX_6716CCAA399E35A6 (comida_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proveedores (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, dir VARCHAR(70) NOT NULL, email VARCHAR(50) NOT NULL, telf INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservas (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, nombre VARCHAR(50) NOT NULL, telefono INT NOT NULL, comensales INT NOT NULL, tipo VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, type_food_id INT DEFAULT NULL, name VARCHAR(55) NOT NULL, description VARCHAR(255) DEFAULT NULL, amount INT NOT NULL, INDEX IDX_4B365660A53A8AA (provider_id), INDEX IDX_4B36566092AFE831 (type_food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tipo_comida (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, icon VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ventas (id INT AUTO_INCREMENT NOT NULL, mesa_id INT NOT NULL, fecha DATETIME NOT NULL, pagado NUMERIC(10, 2) DEFAULT NULL, pago VARCHAR(50) NOT NULL, INDEX IDX_808D9E8BDC7AE9 (mesa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comida ADD CONSTRAINT FK_91AE1CD792AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        // $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660A53A8AA FOREIGN KEY (provider_id) REFERENCES proveedores (id)');
        // $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566092AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE ventas ADD CONSTRAINT FK_808D9E8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comida DROP FOREIGN KEY FK_91AE1CD792AFE831');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA8BDC7AE9');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA399E35A6');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660A53A8AA');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566092AFE831');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
        $this->addSql('DROP TABLE comida');
        $this->addSql('DROP TABLE facturas');
        $this->addSql('DROP TABLE mesas');
        $this->addSql('DROP TABLE pedidos');
        $this->addSql('DROP TABLE proveedores');
        $this->addSql('DROP TABLE reservas');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE tipo_comida');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE ventas');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

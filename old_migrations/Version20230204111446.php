<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204111446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prueba (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(55) NOT NULL, description VARCHAR(255) DEFAULT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proveedores ADD stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proveedores ADD CONSTRAINT FK_864FA8F1DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_864FA8F1DCD6110 ON proveedores (stock_id)');
        $this->addSql('ALTER TABLE tipo_comida ADD stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tipo_comida ADD CONSTRAINT FK_417EACFDCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_417EACFDCD6110 ON tipo_comida (stock_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proveedores DROP FOREIGN KEY FK_864FA8F1DCD6110');
        $this->addSql('ALTER TABLE tipo_comida DROP FOREIGN KEY FK_417EACFDCD6110');
        $this->addSql('DROP TABLE prueba');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP INDEX IDX_864FA8F1DCD6110 ON proveedores');
        $this->addSql('ALTER TABLE proveedores DROP stock_id');
        $this->addSql('DROP INDEX IDX_417EACFDCD6110 ON tipo_comida');
        $this->addSql('ALTER TABLE tipo_comida DROP stock_id');
    }
}

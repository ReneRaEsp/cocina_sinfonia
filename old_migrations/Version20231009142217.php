<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009142217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, type_food_id INT DEFAULT NULL, comida_id INT DEFAULT NULL, name VARCHAR(55) NOT NULL, description VARCHAR(255) DEFAULT NULL, amount INT NOT NULL, INDEX IDX_4B365660A53A8AA (provider_id), INDEX IDX_4B36566092AFE831 (type_food_id), INDEX IDX_4B365660399E35A6 (comida_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660A53A8AA FOREIGN KEY (provider_id) REFERENCES proveedores (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566092AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660A53A8AA');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566092AFE831');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660399E35A6');
        $this->addSql('DROP TABLE stock');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328120324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cajaregistro (id INT AUTO_INCREMENT NOT NULL, dia DATETIME NOT NULL, iniciocaja DOUBLE PRECISION DEFAULT NULL, finalcaja DOUBLE PRECISION DEFAULT NULL, totalcaja DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE caja');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caja (id INT AUTO_INCREMENT NOT NULL, dia DATETIME NOT NULL, iniciocaja DOUBLE PRECISION NOT NULL, finalcaja DOUBLE PRECISION NOT NULL, totalcaja DOUBLE PRECISION NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE cajaregistro');
    }
}

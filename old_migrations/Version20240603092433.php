<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603092433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statscomida (id INT AUTO_INCREMENT NOT NULL, comida_id INT DEFAULT NULL, tipocomida_id INT DEFAULT NULL, mesa_id INT DEFAULT NULL, fecha DATETIME NOT NULL, INDEX IDX_6D0BDDB3399E35A6 (comida_id), INDEX IDX_6D0BDDB3C19B2A0F (tipocomida_id), INDEX IDX_6D0BDDB38BDC7AE9 (mesa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB3399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB3C19B2A0F FOREIGN KEY (tipocomida_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB38BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE ventas ADD CONSTRAINT FK_808D9E8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB3399E35A6');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB3C19B2A0F');
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB38BDC7AE9');
        $this->addSql('DROP TABLE statscomida');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
    }
}

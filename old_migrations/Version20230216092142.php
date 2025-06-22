<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216092142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pedido (id INT AUTO_INCREMENT NOT NULL, mesa_id_id INT NOT NULL, INDEX IDX_C4EC16CE9AC72117 (mesa_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedido_comida (pedido_id INT NOT NULL, comida_id INT NOT NULL, INDEX IDX_353BAD8B4854653A (pedido_id), INDEX IDX_353BAD8B399E35A6 (comida_id), PRIMARY KEY(pedido_id, comida_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CE9AC72117 FOREIGN KEY (mesa_id_id) REFERENCES mesas (id)');
        $this->addSql('ALTER TABLE pedido_comida ADD CONSTRAINT FK_353BAD8B4854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_comida ADD CONSTRAINT FK_353BAD8B399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CE9AC72117');
        $this->addSql('ALTER TABLE pedido_comida DROP FOREIGN KEY FK_353BAD8B4854653A');
        $this->addSql('ALTER TABLE pedido_comida DROP FOREIGN KEY FK_353BAD8B399E35A6');
        $this->addSql('DROP TABLE pedido');
        $this->addSql('DROP TABLE pedido_comida');
    }
}

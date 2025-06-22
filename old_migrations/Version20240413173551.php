<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413173551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE papelera ADD pedidos LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE ventas ADD CONSTRAINT FK_808D9E8BDC7AE9 FOREIGN KEY (mesa_id) REFERENCES mesas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE papelera DROP pedidos');
        $this->addSql('ALTER TABLE ventas DROP FOREIGN KEY FK_808D9E8BDC7AE9');
    }
}

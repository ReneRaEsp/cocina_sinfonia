<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603104802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statscomida ADD tienda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE statscomida ADD CONSTRAINT FK_6D0BDDB319BA6D46 FOREIGN KEY (tienda_id) REFERENCES productostienda (id)');
        $this->addSql('CREATE INDEX IDX_6D0BDDB319BA6D46 ON statscomida (tienda_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statscomida DROP FOREIGN KEY FK_6D0BDDB319BA6D46');
        $this->addSql('DROP INDEX IDX_6D0BDDB319BA6D46 ON statscomida');
        $this->addSql('ALTER TABLE statscomida DROP tienda_id');
    }
}

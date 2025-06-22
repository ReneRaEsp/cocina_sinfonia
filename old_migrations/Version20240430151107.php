<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430151107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos ADD producttienda_id INT DEFAULT NULL, CHANGE comida_id comida_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAFCCA42B1 FOREIGN KEY (producttienda_id) REFERENCES productostienda (id)');
        $this->addSql('CREATE INDEX IDX_6716CCAAFCCA42B1 ON pedidos (producttienda_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAFCCA42B1');
        $this->addSql('DROP INDEX IDX_6716CCAAFCCA42B1 ON pedidos');
        $this->addSql('ALTER TABLE pedidos DROP producttienda_id, CHANGE comida_id comida_id INT NOT NULL');
    }
}

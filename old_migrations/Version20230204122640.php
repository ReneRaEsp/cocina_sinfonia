<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204122640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD proveedores_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_STOCK_PROVIDERS FOREIGN KEY (proveedores_id) REFERENCES proveedores (id)');
        $this->addSql('CREATE INDEX IDX_STOCK_PROVIDERS ON stock (proveedores_id)');
        $this->addSql('ALTER TABLE stock ADD tipo_comida_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_STOCK_TYPECOMIDA FOREIGN KEY (tipo_comida_id) REFERENCES tipo_comida (id)');
        $this->addSql('CREATE INDEX IDX_STOCK_TYPECOMIDA ON stock (tipo_comida_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_STOCK_PROVIDERS');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_STOCK_TYPECOMIDA');
        $this->addSql('DROP INDEX IDX_STOCK_PROVIDERS ON stock');
        $this->addSql('DROP INDEX IDX_STOCK_TYPECOMIDA ON stock');
        $this->addSql('ALTER TABLE stock DROP proveedores_id');
        $this->addSql('ALTER TABLE stock DROP tipo_comida_id');
    }
}

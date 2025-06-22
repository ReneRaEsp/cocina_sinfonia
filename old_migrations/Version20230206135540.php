<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206135540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        
        $this->addSql('ALTER TABLE stock ADD provider_id INT DEFAULT NULL, ADD type_food_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660A53A8AA FOREIGN KEY (provider_id) REFERENCES proveedores (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566092AFE831 FOREIGN KEY (type_food_id) REFERENCES tipo_comida (id)');
        $this->addSql('CREATE INDEX IDX_4B365660A53A8AA ON stock (provider_id)');
        $this->addSql('CREATE INDEX IDX_4B36566092AFE831 ON stock (type_food_id)');
    }

    public function down(Schema $schema): void
    {
        // $this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660A53A8AA');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566092AFE831');
        $this->addSql('DROP INDEX IDX_4B365660A53A8AA ON stock');
        $this->addSql('DROP INDEX IDX_4B36566092AFE831 ON stock');
        $this->addSql('ALTER TABLE stock ADD proveedores_id INT DEFAULT NULL, ADD tipo_comida_id INT DEFAULT NULL, DROP provider_id, DROP type_food_id');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_STOCK_TYPECOMIDA FOREIGN KEY (tipo_comida_id) REFERENCES tipo_comida (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_STOCK_PROVIDERS FOREIGN KEY (proveedores_id) REFERENCES proveedores (id)');
        $this->addSql('CREATE INDEX IDX_STOCK_PROVIDERS ON stock (proveedores_id)');
        $this->addSql('CREATE INDEX IDX_STOCK_TYPECOMIDA ON stock (tipo_comida_id)');
    }
}

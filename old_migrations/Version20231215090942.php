<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215090942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichaje CHANGE inicio_am inicio_am TIME  NULL, CHANGE fin_am fin_am TIME  NULL, CHANGE inicio_pm inicio_pm TIME  NULL, CHANGE fin_pm fin_pm TIME  NULL');
        $this->addSql('ALTER TABLE tipo_comida ADD active TINYINT(1)  NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichaje CHANGE inicio_am inicio_am TIME DEFAULT NULL, CHANGE fin_am fin_am TIME DEFAULT NULL, CHANGE inicio_pm inicio_pm TIME DEFAULT NULL, CHANGE fin_pm fin_pm TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tipo_comida DROP active');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325101107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP id_etat');
        $this->addSql('ALTER TABLE sortie MODIFY id_sortie INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON sortie');
        $this->addSql('ALTER TABLE sortie CHANGE id_sortie id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sortie ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE ville DROP id_ville');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat ADD id_etat INT NOT NULL');
        $this->addSql('ALTER TABLE sortie MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON sortie');
        $this->addSql('ALTER TABLE sortie CHANGE id id_sortie INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sortie ADD PRIMARY KEY (id_sortie)');
        $this->addSql('ALTER TABLE ville ADD id_ville INT NOT NULL');
    }
}

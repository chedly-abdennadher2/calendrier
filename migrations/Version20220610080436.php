<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610080436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE suivi_conge (id INT AUTO_INCREMENT NOT NULL, employe_id INT NOT NULL, contrat_id INT NOT NULL, annee INT NOT NULL, mois INT NOT NULL, quota DOUBLE PRECISION NOT NULL, nbjourpris INT DEFAULT NULL, nbjourrestant INT NOT NULL, INDEX IDX_8DD4B36B1B65292 (employe_id), INDEX IDX_8DD4B36B1823061F (contrat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE suivi_conge ADD CONSTRAINT FK_8DD4B36B1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE suivi_conge ADD CONSTRAINT FK_8DD4B36B1823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE suivi_conge');
    }
}

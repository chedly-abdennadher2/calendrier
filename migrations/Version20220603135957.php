<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603135957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, salaire DOUBLE PRECISION NOT NULL, role JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conge (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT DEFAULT NULL, datedebut DATE NOT NULL, datefin DATE NOT NULL, state VARCHAR(10) NOT NULL, INDEX IDX_2ED893487EE5403C (administrateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, conge_id INT DEFAULT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) DEFAULT NULL, quota INT DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_F804D3B9CAAC9A59 (conge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED893487EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9CAAC9A59 FOREIGN KEY (conge_id) REFERENCES conge (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED893487EE5403C');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9CAAC9A59');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE conge');
        $this->addSql('DROP TABLE employe');
    }
}

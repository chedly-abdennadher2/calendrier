<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220624121731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, login_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, salaire DOUBLE PRECISION NOT NULL, role JSON NOT NULL, UNIQUE INDEX UNIQ_32EB52E85CB2E05D (login_id), UNIQUE INDEX UNIQ_32EB52E8642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conge (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, datedebut DATE NOT NULL, datefin DATE NOT NULL, state VARCHAR(10) NOT NULL, typeconge VARCHAR(20) NOT NULL, INDEX IDX_2ED89348A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, datedebut DATE NOT NULL, datefin DATE NOT NULL, datearret DATE DEFAULT NULL, typedecontrat VARCHAR(10) NOT NULL, quotaparmoisaccorde DOUBLE PRECISION NOT NULL, quotarestant INT DEFAULT NULL, statut VARCHAR(20) DEFAULT NULL, INDEX IDX_60349993A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) DEFAULT NULL, quota INT DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, nbjourpris INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suivi_conge (id INT AUTO_INCREMENT NOT NULL, contrat_id INT NOT NULL, user_id INT NOT NULL, annee INT NOT NULL, mois INT NOT NULL, quota DOUBLE PRECISION NOT NULL, nbjourpris INT DEFAULT NULL, nbjourrestant INT DEFAULT NULL, INDEX IDX_8DD4B36B1823061F (contrat_id), INDEX IDX_8DD4B36BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nomutilisateur VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) DEFAULT NULL, quota INT DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, nbjourpris INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6497F1813BC (nomutilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E85CB2E05D FOREIGN KEY (login_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED89348A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE suivi_conge ADD CONSTRAINT FK_8DD4B36B1823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE suivi_conge ADD CONSTRAINT FK_8DD4B36BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8642B8210');
        $this->addSql('ALTER TABLE suivi_conge DROP FOREIGN KEY FK_8DD4B36B1823061F');
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E85CB2E05D');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED89348A76ED395');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993A76ED395');
        $this->addSql('ALTER TABLE suivi_conge DROP FOREIGN KEY FK_8DD4B36BA76ED395');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE conge');
        $this->addSql('DROP TABLE contrat');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE suivi_conge');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

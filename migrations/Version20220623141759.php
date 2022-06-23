<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220623141759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur ADD admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32EB52E8642B8210 ON administrateur (admin_id)');
        $this->addSql('ALTER TABLE conge ADD administrateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED893487EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('CREATE INDEX IDX_2ED893487EE5403C ON conge (administrateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8642B8210');
        $this->addSql('DROP INDEX UNIQ_32EB52E8642B8210 ON administrateur');
        $this->addSql('ALTER TABLE administrateur DROP admin_id');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED893487EE5403C');
        $this->addSql('DROP INDEX IDX_2ED893487EE5403C ON conge');
        $this->addSql('ALTER TABLE conge DROP administrateur_id');
    }
}

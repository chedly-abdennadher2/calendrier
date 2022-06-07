<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607073258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employe ADD login_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B95CB2E05D FOREIGN KEY (login_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B95CB2E05D ON employe (login_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B95CB2E05D');
        $this->addSql('DROP INDEX UNIQ_F804D3B95CB2E05D ON employe');
        $this->addSql('ALTER TABLE employe DROP login_id');
    }
}

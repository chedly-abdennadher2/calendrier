<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607071431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur ADD login_id INT NOT NULL');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E85CB2E05D FOREIGN KEY (login_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32EB52E85CB2E05D ON administrateur (login_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E85CB2E05D');
        $this->addSql('DROP INDEX UNIQ_32EB52E85CB2E05D ON administrateur');
        $this->addSql('ALTER TABLE administrateur DROP login_id');
    }
}

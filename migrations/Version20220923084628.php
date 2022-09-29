<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220923084628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE departure DROP CONSTRAINT fk_45e9c6714d7b7542');
        $this->addSql('DROP INDEX idx_45e9c6714d7b7542');
        $this->addSql('ALTER TABLE departure RENAME COLUMN line_id TO route_id');
        $this->addSql('ALTER TABLE departure ADD CONSTRAINT FK_45E9C67134ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_45E9C67134ECB4E6 ON departure (route_id)');
        $this->addSql('ALTER TABLE line DROP synced_at');
        $this->addSql('ALTER TABLE route ADD system_name VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE route ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE route ADD synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN route.synced_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stop ADD system_name INT NOT NULL');
        $this->addSql('ALTER TABLE stop ADD lon DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stop ADD lat DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE line ADD synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN line.synced_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE departure DROP CONSTRAINT FK_45E9C67134ECB4E6');
        $this->addSql('DROP INDEX IDX_45E9C67134ECB4E6');
        $this->addSql('ALTER TABLE departure RENAME COLUMN route_id TO line_id');
        $this->addSql('ALTER TABLE departure ADD CONSTRAINT fk_45e9c6714d7b7542 FOREIGN KEY (line_id) REFERENCES line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_45e9c6714d7b7542 ON departure (line_id)');
        $this->addSql('ALTER TABLE route DROP system_name');
        $this->addSql('ALTER TABLE route DROP description');
        $this->addSql('ALTER TABLE route DROP synced_at');
        $this->addSql('ALTER TABLE stop DROP system_name');
        $this->addSql('ALTER TABLE stop DROP lon');
        $this->addSql('ALTER TABLE stop DROP lat');
    }
}

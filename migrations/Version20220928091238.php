<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928091238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SEQUENCE stop_time_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        // $this->addSql('CREATE SEQUENCE trip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE stop_time (id int GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY, trip_id INT NOT NULL, stop_id INT NOT NULL, synced_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, departure_at TIME(0) WITHOUT TIME ZONE NOT NULL)');
        $this->addSql('CREATE INDEX IDX_85725A5AA5BC2E0E ON stop_time (trip_id)');
        $this->addSql('CREATE INDEX IDX_85725A5A3902063D ON stop_time (stop_id)');
        $this->addSql('COMMENT ON COLUMN stop_time.synced_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN stop_time.departure_at IS \'(DC2Type:time_immutable)\'');
        $this->addSql('CREATE TABLE trip (id int GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY, line_id INT NOT NULL, day INT NOT NULL, system_name VARCHAR(255) NOT NULL, header VARCHAR(255) NOT NULL, status VARCHAR(60) DEFAULT NULL, synced_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');
        $this->addSql('CREATE INDEX IDX_7656F53B4D7B7542 ON trip (line_id)');
        $this->addSql('COMMENT ON COLUMN trip.synced_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stop_time ADD CONSTRAINT FK_85725A5AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stop_time ADD CONSTRAINT FK_85725A5A3902063D FOREIGN KEY (stop_id) REFERENCES stop (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B4D7B7542 FOREIGN KEY (line_id) REFERENCES line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE departure ALTER start_at TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE departure ALTER start_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN departure.start_at IS \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stop_time DROP CONSTRAINT FK_85725A5AA5BC2E0E');
        $this->addSql('DROP SEQUENCE stop_time_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE trip_id_seq CASCADE');
        $this->addSql('DROP TABLE stop_time');
        $this->addSql('DROP TABLE trip');
        $this->addSql('ALTER TABLE departure ALTER start_at TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE departure ALTER start_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN departure.start_at IS NULL');
    }
}

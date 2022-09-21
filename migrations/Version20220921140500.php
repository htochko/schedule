<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921140500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE departure_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE line_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE route_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE stop_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE departure (id INT NOT NULL, line_id INT NOT NULL, day VARCHAR(60) NOT NULL, direction BOOLEAN NOT NULL, start_at TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_45E9C6714D7B7542 ON departure (line_id)');
        $this->addSql('CREATE TABLE line (id INT NOT NULL, name VARCHAR(255) NOT NULL, synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN line.synced_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE route (id INT NOT NULL, line_id INT NOT NULL, stop_id INT NOT NULL, direction BOOLEAN NOT NULL, interval VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C420794D7B7542 ON route (line_id)');
        $this->addSql('CREATE INDEX IDX_2C420793902063D ON route (stop_id)');
        $this->addSql('COMMENT ON COLUMN route.interval IS \'(DC2Type:dateinterval)\'');
        $this->addSql('CREATE TABLE stop (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE departure ADD CONSTRAINT FK_45E9C6714D7B7542 FOREIGN KEY (line_id) REFERENCES line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420794D7B7542 FOREIGN KEY (line_id) REFERENCES line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420793902063D FOREIGN KEY (stop_id) REFERENCES stop (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE departure DROP CONSTRAINT FK_45E9C6714D7B7542');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT FK_2C420794D7B7542');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT FK_2C420793902063D');
        $this->addSql('DROP SEQUENCE departure_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE line_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE route_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE stop_id_seq CASCADE');
        $this->addSql('DROP TABLE departure');
        $this->addSql('DROP TABLE line');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE stop');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221133102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD COLUMN done BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, color, difficulty, created_at, periodicity, days, is_group_task, UserUuid, GroupUuid FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(250) NOT NULL, description CLOB DEFAULT NULL, color VARCHAR(6) NOT NULL, difficulty INTEGER NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , periodicity VARCHAR(255) NOT NULL, days VARCHAR(255) DEFAULT NULL, is_group_task BOOLEAN NOT NULL, UserUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , GroupUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , CONSTRAINT FK_527EDB252E46E5BF FOREIGN KEY (UserUuid) REFERENCES users (user_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB253D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (group_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description, color, difficulty, created_at, periodicity, days, is_group_task, UserUuid, GroupUuid) SELECT id, title, description, color, difficulty, created_at, periodicity, days, is_group_task, UserUuid, GroupUuid FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB252E46E5BF ON task (UserUuid)');
        $this->addSql('CREATE INDEX IDX_527EDB253D458CB8 ON task (GroupUuid)');
    }
}

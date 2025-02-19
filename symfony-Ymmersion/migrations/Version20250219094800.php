<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219094800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__group_logs AS SELECT id, addition, point, GroupUuid FROM group_logs');
        $this->addSql('DROP TABLE group_logs');
        $this->addSql('CREATE TABLE group_logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, addition BOOLEAN NOT NULL, point INTEGER NOT NULL, GroupUuid CHAR(36) NOT NULL --(DC2Type:guid)
        , CONSTRAINT FK_D9D59D2C3D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (group_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO group_logs (id, addition, point, GroupUuid) SELECT id, addition, point, GroupUuid FROM __temp__group_logs');
        $this->addSql('DROP TABLE __temp__group_logs');
        $this->addSql('CREATE INDEX IDX_D9D59D2C3D458CB8 ON group_logs (GroupUuid)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT group_uuid, name, point, Creator FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (group_uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name CLOB NOT NULL, point INTEGER NOT NULL, creator CLOB NOT NULL, PRIMARY KEY(group_uuid))');
        $this->addSql('INSERT INTO groups (group_uuid, name, point, creator) SELECT group_uuid, name, point, Creator FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invitation AS SELECT id, Sender, Recever, WhichGroup FROM invitation');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('CREATE TABLE invitation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Sender CHAR(36) NOT NULL --(DC2Type:guid)
        , Recever CHAR(36) NOT NULL --(DC2Type:guid)
        , WhichGroup CHAR(36) NOT NULL --(DC2Type:guid)
        , CONSTRAINT FK_F11D61A258AC4FF9 FOREIGN KEY (Sender) REFERENCES users (user_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A2558828B5 FOREIGN KEY (Recever) REFERENCES users (user_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A2A4872AF8 FOREIGN KEY (WhichGroup) REFERENCES groups (group_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invitation (id, Sender, Recever, WhichGroup) SELECT id, Sender, Recever, WhichGroup FROM __temp__invitation');
        $this->addSql('DROP TABLE __temp__invitation');
        $this->addSql('CREATE INDEX IDX_F11D61A2A4872AF8 ON invitation (WhichGroup)');
        $this->addSql('CREATE INDEX IDX_F11D61A2558828B5 ON invitation (Recever)');
        $this->addSql('CREATE INDEX IDX_F11D61A258AC4FF9 ON invitation (Sender)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, color, periodicity, UserUuid, GroupUuid FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(250) NOT NULL, description CLOB DEFAULT NULL, color VARCHAR(6) NOT NULL, periodicity VARCHAR(255) NOT NULL, UserUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , GroupUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , CONSTRAINT FK_527EDB252E46E5BF FOREIGN KEY (UserUuid) REFERENCES users (user_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB253D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (group_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description, color, periodicity, UserUuid, GroupUuid) SELECT id, title, description, color, periodicity, UserUuid, GroupUuid FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB253D458CB8 ON task (GroupUuid)');
        $this->addSql('CREATE INDEX IDX_527EDB252E46E5BF ON task (UserUuid)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT user_uuid, email, pseudo, pwd, last_connection, GroupUuid FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (user_uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , email CLOB NOT NULL, pseudo CLOB NOT NULL, pwd CLOB NOT NULL, last_connection DATETIME NOT NULL, GroupUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(user_uuid), CONSTRAINT FK_1483A5E93D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (group_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (user_uuid, email, pseudo, pwd, last_connection, GroupUuid) SELECT user_uuid, email, pseudo, pwd, last_connection, GroupUuid FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE INDEX IDX_1483A5E93D458CB8 ON users (GroupUuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E986CC499D ON users (pseudo)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__group_logs AS SELECT id, addition, point, GroupUuid FROM group_logs');
        $this->addSql('DROP TABLE group_logs');
        $this->addSql('CREATE TABLE group_logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, addition BOOLEAN NOT NULL, point INTEGER NOT NULL, GroupUuid CHAR(36) NOT NULL --(DC2Type:guid)
        , CONSTRAINT FK_D9D59D2C3D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (GroupUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO group_logs (id, addition, point, GroupUuid) SELECT id, addition, point, GroupUuid FROM __temp__group_logs');
        $this->addSql('DROP TABLE __temp__group_logs');
        $this->addSql('CREATE INDEX IDX_D9D59D2C3D458CB8 ON group_logs (GroupUuid)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT group_uuid, name, point, creator FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (group_uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name CLOB NOT NULL, point INTEGER NOT NULL, Creator CHAR(36) NOT NULL --(DC2Type:guid)
        , PRIMARY KEY(group_uuid), CONSTRAINT FK_F06D397073BBD3FF FOREIGN KEY (Creator) REFERENCES users (UserUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groups (group_uuid, name, point, Creator) SELECT group_uuid, name, point, creator FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F06D397073BBD3FF ON groups (Creator)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invitation AS SELECT id, Sender, Recever, WhichGroup FROM invitation');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('CREATE TABLE invitation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, Sender CHAR(36) NOT NULL --(DC2Type:guid)
        , Recever CHAR(36) NOT NULL --(DC2Type:guid)
        , WhichGroup CHAR(36) NOT NULL --(DC2Type:guid)
        , CONSTRAINT FK_F11D61A258AC4FF9 FOREIGN KEY (Sender) REFERENCES users (UserUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A2558828B5 FOREIGN KEY (Recever) REFERENCES users (UserUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A2A4872AF8 FOREIGN KEY (WhichGroup) REFERENCES groups (GroupUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invitation (id, Sender, Recever, WhichGroup) SELECT id, Sender, Recever, WhichGroup FROM __temp__invitation');
        $this->addSql('DROP TABLE __temp__invitation');
        $this->addSql('CREATE INDEX IDX_F11D61A258AC4FF9 ON invitation (Sender)');
        $this->addSql('CREATE INDEX IDX_F11D61A2558828B5 ON invitation (Recever)');
        $this->addSql('CREATE INDEX IDX_F11D61A2A4872AF8 ON invitation (WhichGroup)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, color, periodicity, UserUuid, GroupUuid FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(250) NOT NULL, description CLOB DEFAULT NULL, color VARCHAR(6) NOT NULL, periodicity VARCHAR(255) NOT NULL, UserUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , GroupUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , CONSTRAINT FK_527EDB252E46E5BF FOREIGN KEY (UserUuid) REFERENCES users (UserUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB253D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (GroupUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description, color, periodicity, UserUuid, GroupUuid) SELECT id, title, description, color, periodicity, UserUuid, GroupUuid FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB252E46E5BF ON task (UserUuid)');
        $this->addSql('CREATE INDEX IDX_527EDB253D458CB8 ON task (GroupUuid)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT user_uuid, email, pseudo, pwd, last_connection, GroupUuid FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (user_uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , email CLOB NOT NULL, pseudo CLOB NOT NULL, pwd CLOB NOT NULL, last_connection DATETIME NOT NULL, GroupUuid CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(user_uuid), CONSTRAINT FK_1483A5E93D458CB8 FOREIGN KEY (GroupUuid) REFERENCES groups (GroupUuid) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (user_uuid, email, pseudo, pwd, last_connection, GroupUuid) SELECT user_uuid, email, pseudo, pwd, last_connection, GroupUuid FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E986CC499D ON users (pseudo)');
        $this->addSql('CREATE INDEX IDX_1483A5E93D458CB8 ON users (GroupUuid)');
    }
}

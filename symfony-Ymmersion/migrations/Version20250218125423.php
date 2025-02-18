<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218125423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT ID, GroupsUuid, name, Creator, point FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, GroupsUuid TEXT NOT NULL, Name VARCHAR(200) NOT NULL, Creator TEXT NOT NULL, Points INTEGER NOT NULL)');
        $this->addSql('INSERT INTO groups (ID, GroupsUuid, Name, Creator, Points) SELECT ID, GroupsUuid, Name, Creator, Point FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, groups_uuid, name, creator, points FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groups_uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name CLOB NOT NULL, creator CHAR(36) NOT NULL --(DC2Type:guid)
        , point INTEGER NOT NULL)');
        $this->addSql('INSERT INTO groups (id, groups_uuid, name, creator, point) SELECT id, groups_uuid, name, creator, points FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218131555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT groups_uuid, name, creator, points FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (groups_uuid TEXT NOT NULL, name VARCHAR(200) NOT NULL, creator TEXT NOT NULL, points INTEGER NOT NULL, PRIMARY KEY(groups_uuid))');
        $this->addSql('INSERT INTO groups (groups_uuid, name, creator, points) SELECT groups_uuid, name, creator, points FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT email, password, is_verified, username, profile_picture FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (user_uuid VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, username VARCHAR(50) NOT NULL, profile_picture BLOB DEFAULT NULL, PRIMARY KEY(user_uuid))');
        $this->addSql('INSERT INTO user (email, password, is_verified, username, profile_picture) SELECT email, password, is_verified, username, profile_picture FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT groups_uuid, name, creator, points FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groups_uuid TEXT NOT NULL, name VARCHAR(200) NOT NULL, creator TEXT NOT NULL, points INTEGER NOT NULL)');
        $this->addSql('INSERT INTO groups (groups_uuid, name, creator, points) SELECT groups_uuid, name, creator, points FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT email, password, is_verified, username, profile_picture FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, username VARCHAR(50) NOT NULL, profile_picture BLOB DEFAULT NULL, roles TEXT NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO user (email, password, is_verified, username, profile_picture) SELECT email, password, is_verified, username, profile_picture FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }
}

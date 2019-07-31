<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190731153802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create table MBTI Test';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE mbti_test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mbti_test (id INT NOT NULL, user_id INT NOT NULL, step INT NOT NULL, completed BOOLEAN NOT NULL, result VARCHAR(10) DEFAULT NULL, ei INT DEFAULT NULL, ns INT DEFAULT NULL, tf INT DEFAULT NULL, pj INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD4F863BA76ED395 ON mbti_test (user_id)');
        $this->addSql('ALTER TABLE mbti_test ADD CONSTRAINT FK_AD4F863BA76ED395 FOREIGN KEY (user_id) REFERENCES facebook_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE mbti_test_id_seq CASCADE');
        $this->addSql('DROP TABLE mbti_test');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190801213436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create Mbti answer table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE mbti_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mbti_answer (id INT NOT NULL, test_id INT NOT NULL, step INT NOT NULL, value VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F9BCE50F1E5D0459 ON mbti_answer (test_id)');
        $this->addSql('ALTER TABLE mbti_answer ADD CONSTRAINT FK_F9BCE50F1E5D0459 FOREIGN KEY (test_id) REFERENCES mbti_test (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE mbti_answer_id_seq CASCADE');
        $this->addSql('DROP TABLE mbti_answer');
    }
}

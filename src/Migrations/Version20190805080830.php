<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190805080830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Cascade should work';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mbti_answer DROP CONSTRAINT FK_F9BCE50F1E5D0459');
        $this->addSql('ALTER TABLE mbti_answer ADD CONSTRAINT FK_F9BCE50F1E5D0459 FOREIGN KEY (test_id) REFERENCES mbti_test (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mbti_answer DROP CONSTRAINT fk_f9bce50f1e5d0459');
        $this->addSql('ALTER TABLE mbti_answer ADD CONSTRAINT fk_f9bce50f1e5d0459 FOREIGN KEY (test_id) REFERENCES mbti_test (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

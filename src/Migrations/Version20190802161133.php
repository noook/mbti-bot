<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190802161133 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Separates dichtomies in DB';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mbti_test ADD e INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD i INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD n INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD s INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD t INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD f INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD p INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD j INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test DROP ei');
        $this->addSql('ALTER TABLE mbti_test DROP ns');
        $this->addSql('ALTER TABLE mbti_test DROP tf');
        $this->addSql('ALTER TABLE mbti_test DROP pj');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mbti_test ADD ei INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD ns INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD tf INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test ADD pj INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mbti_test DROP e');
        $this->addSql('ALTER TABLE mbti_test DROP i');
        $this->addSql('ALTER TABLE mbti_test DROP n');
        $this->addSql('ALTER TABLE mbti_test DROP s');
        $this->addSql('ALTER TABLE mbti_test DROP t');
        $this->addSql('ALTER TABLE mbti_test DROP f');
        $this->addSql('ALTER TABLE mbti_test DROP p');
        $this->addSql('ALTER TABLE mbti_test DROP j');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190812063929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Authorizes null values on answers';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mbti_answer ALTER value DROP NOT NULL');
        $this->addSql('ALTER TABLE mbti_answer ALTER question SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mbti_answer ALTER value SET NOT NULL');
        $this->addSql('ALTER TABLE mbti_answer ALTER question DROP NOT NULL');
    }
}

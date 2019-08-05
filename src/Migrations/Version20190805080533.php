<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190805080533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Now deletes answers on MBTI test remove';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mbti_test DROP CONSTRAINT FK_AD4F863BA76ED395');
        $this->addSql('ALTER TABLE mbti_test ADD CONSTRAINT FK_AD4F863BA76ED395 FOREIGN KEY (user_id) REFERENCES facebook_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mbti_test DROP CONSTRAINT fk_ad4f863ba76ed395');
        $this->addSql('ALTER TABLE mbti_test ADD CONSTRAINT fk_ad4f863ba76ed395 FOREIGN KEY (user_id) REFERENCES facebook_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

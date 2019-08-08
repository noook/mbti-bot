<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\MbtiAnswer;

final class Version20190808071855 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function getDescription() : string
    {
        return 'Prepares shuffled questions';
    }

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getEntityManager(): ObjectManager
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mbti_answer ADD question INT');
    }

    public function postUp(Schema $schema): void
    {
        $em = $this->getEntityManager();
        $mbtiAnswerRepository = $em->getRepository(MbtiAnswer::class);

        foreach ($mbtiAnswerRepository->findAll() as $answer) {
            $answer->setQuestion($answer->getStep());
        }

        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mbti_answer DROP question');
    }
}

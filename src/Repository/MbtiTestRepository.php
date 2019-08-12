<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\FacebookUser;
use App\Entity\MbtiTest;
use App\Helper\MbtiHelper;
use App\Entity\MbtiAnswer;

/**
 * @method MbtiTest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MbtiTest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MbtiTest[]    findAll()
 * @method MbtiTest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MbtiTestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MbtiTest::class);
    }

    public function createTest(FacebookUser $user): MbtiTest
    {
        $test = (new MbtiTest)
                ->setCompleted(false)
                ->setStep(1)
                ->setUser($user);

        $answers = [];

        for ($i = 1; $i <= MbtiHelper::TEST_LENGTH; $i++) {
            $answers[$i] = (new MbtiAnswer)
                ->setQuestion($i)
                ->setValue(null)
                ->setTest($test);
        }
        shuffle($answers);

        foreach ($answers as $step => $answer) {
            $answer->setStep($step + 1);
            $this->_em->persist($answer);
        }
        
        $this->_em->persist($test);
        $this->_em->flush();

        return $test;
    }

    public function currentTest(FacebookUser $user): ?MbtiTest
    {
        return $this->findOneBy([
            'completed' => false,
            'user' => $user,
        ]);
    }

    public function nextStep(MbtiTest $test)
    {
        if ($test->getStep() + 1 === 41) {
            $test
                ->setCompleted(true);
        } else {
            $test
                ->setStep($test->getStep() + 1);
        }

        $this->_em->flush();
    }

    public function saveEndResults(MbtiTest $test, array $results)
    {
        $test
            ->setCompletedAt(new \DateTimeImmutable())
            ->setI($results['I'])
            ->setE($results['E'])
            ->setN($results['N'])
            ->setS($results['S'])
            ->setT($results['T'])
            ->setF($results['F'])
            ->setP($results['P'])
            ->setJ($results['J']);

        $this->_em->flush();
    }

    public function findLatestCompleted(FacebookUser $user): MbtiTest
    {
        $qb = $this->createQueryBuilder('test');

        $qb
            ->orderBy('test.completed_at', 'DESC')
            ->where('test.user = ?1')
            ->setParameter(1, $user)
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    public function deleteCurrent(FacebookUser $user)
    {
        $test = $this->findOneBy([
            'completed' => false,
            'user' => $user,
        ]);

        $this->_em->remove($test);
        $this->_em->flush();
    }
}

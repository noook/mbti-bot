<?php

namespace App\Repository;

use App\Entity\MbtiTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\FacebookUser;

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

    public function currentTest(FacebookUser $user): ?MbtiTest
    {
        return $this->findOneBy([
            'completed' => false,
            'user' => $user,
        ]);
    }
}

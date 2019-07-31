<?php

namespace App\Repository;

use App\Entity\MbtiQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MbtiQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method MbtiQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method MbtiQuestion[]    findAll()
 * @method MbtiQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MbtiQuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MbtiQuestion::class);
    }
}

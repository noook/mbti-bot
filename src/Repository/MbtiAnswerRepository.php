<?php

namespace App\Repository;

use App\Entity\MbtiAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MbtiAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MbtiAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MbtiAnswer[]    findAll()
 * @method MbtiAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MbtiAnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MbtiAnswer::class);
    }

    public function saveAnswer(MbtiAnswer $answer)
    {
        $this->_em->flush();
    }
}

<?php

namespace App\Repository;

use App\Entity\FacebookUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FacebookUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacebookUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacebookUser[]    findAll()
 * @method FacebookUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacebookUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FacebookUser::class);
    }

    public function insertOrUpdate(FacebookUser $user): FacebookUser
    {
        $result = $this->findOneBy(['fbid' => $user->getFbid()]);

        if (null === $result) {
            $this->_em->persist($user);
        } else {
            $result
                ->setFbid($user->getFbid())
                ->setFirstname($user->getFirstname())
                ->setLastname($user->getLastname())
                ->setLastActive($user->getLastActive());
        }

        $this->_em->flush();

        return $user;
    }
}

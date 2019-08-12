<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\FacebookUser;
use App\Entity\MbtiAnswer;
use App\Helper\MbtiHelper;
use App\Entity\MbtiTest;

class MbtiFixtures extends Fixture
{
    public function load(ObjectManager $em)
    {
        $user = (new FacebookUser)
            ->setFirstname('Neil')
            ->setFbid('2310242789095020')
            ->setLastActive(new \DateTimeImmutable())
            ->setLastname('Richter')
            ->setLocale('fr_FR');
        $em->persist($user);
        $test = (new MbtiTest)
            ->setCompleted(true)
            ->setUser($user)
            ->setResult('ISTP')
            ->setStep(MbtiHelper::TEST_LENGTH)
            ->setE(2)
            ->setI(8)
            ->setN(2)
            ->setS(8)
            ->setF(3)
            ->setT(7)
            ->setP(9)
            ->setJ(1);
        $em->persist($test);

        $step = 1;
        $dichotomies = [
            [
                'I' => 8,
                'E' => 2
            ],
            [
                'S' => 8,
                'N' => 2,
            ],
            [
                'T' => 7,
                'F' => 3,
            ],
            [
                'J' => 1,
                'P' => 9,
            ],
        ];

        foreach ($dichotomies as $pair) {
            foreach ($pair as $dichotomy => $occurr) {
                for ($i = 0; $i < $occurr; $i++) {
                    $answer = (new MbtiAnswer)
                        ->setTest($test)
                        ->setStep($step)
                        ->setValue($dichotomy);

                    $em->persist($answer);
                    ++$step;
                }
            }
        }

        $em->flush();
    }
}

<?php

namespace App\Handler\Interaction\MbtiContext;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\MbtiTest;
use App\Golem\GolemResponse;
use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiTestRepository;

class MbtiTestInteractionHandler implements InteractionHandlerInterface
{
    private $facebookUserRepository;
    private $mbtiTestRepository;
    private $em;
    private $translator;

    public function __construct(
        FacebookUserRepository $facebookUserRepository,
        MbtiTestRepository $mbtiTestRepository,
        ObjectManager $em,
        TranslatorInterface $translator
    )
    {
        $this->facebookUserRepository = $facebookUserRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->em = $em;
        $this->translator = $translator;
    }
    public function getAlias(): string
    {
        return InteractionHandlerAliases::MBTI_TEST;
    }

    public function handleInteraction(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $messengerRequest->getSender()]);
        $test = $this->mbtiTestRepository->currentTest($user);
        $messages = [];

        if (null === $test) {
            $test = (new MbtiTest)
                ->setCompleted(false)
                ->setStep(1)
                ->setUser($user);
            $this->em->persist($test);
            $this->em->flush();

            $startMessages = ['lets_start_test', 'test_is_40_questions_long', 'answer_like_this'];
            foreach ($startMessages as $message) {
                $messages[] = [
                    'text' => $this->translator->trans($message, [], null, $user->getLocale()),
                ];
            }
        } else if ($test->getStep() > 1) {
            $messages[] = [
                'text' => $this->translator->trans('test_already_started', [], null, $user->getLocale()),
            ];
        }

        dd($messages);
    }
}
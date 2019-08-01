<?php

namespace App\Handler\Interaction\MbtiContext;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\MessageFormatterCollection;
use App\Entity\MbtiTest;
use App\Formatter\MessageFormatterAliases;
use App\Golem\GolemResponse;
use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Helper\MbtiHelper;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiTestRepository;

class MbtiTestInteractionHandler implements InteractionHandlerInterface
{
    private $messenger;
    private $facebookUserRepository;
    private $mbtiTestRepository;
    private $mbtiHelper;
    private $messageFormatterCollection;
    private $em;
    private $translator;

    public function __construct(
        MessengerApi $messenger,
        FacebookUserRepository $facebookUserRepository,
        MbtiTestRepository $mbtiTestRepository,
        MbtiHelper $mbtiHelper,
        MessageFormatterCollection $messageFormatterCollection,
        ObjectManager $em,
        TranslatorInterface $translator
    )
    {
        $this->messenger = $messenger;
        $this->facebookUserRepository = $facebookUserRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->mbtiHelper = $mbtiHelper;
        $this->messageFormatterCollection = $messageFormatterCollection;
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
                    'type' => MessageFormatterAliases::TEXT,
                    'text' => $this->translator->trans($message, [], null, $user->getLocale()),
                ];
            }
        }

        if ($test->getStep() > 1) {
            $messages[] = [
                'type' => MessageFormatterAliases::TEXT,
                'text' => $this->translator->trans('test_already_started', [], null, $user->getLocale()),
            ];
        } else {
            $questions = $this->mbtiHelper->getNextQuestion($test);
            $messages[] = $this->mbtiHelper->prepareQuestion($questions, $user);
        }

        foreach ($messages as $message) {
            $element = $this
                ->messageFormatterCollection
                ->get($message['type'])
                ->format($message);
            $this
                ->messenger
                ->setRecipient($user->getFbid())
                ->setTyping('on');
            sleep(1);
            $this
                ->messenger
                ->sendMessage($element)
                ->setTyping('off');
        }
    }
}
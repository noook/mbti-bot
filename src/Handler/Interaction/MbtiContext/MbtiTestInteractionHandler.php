<?php

namespace App\Handler\Interaction\MbtiContext;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\MessageFormatterCollection;
use App\Formatter\MessageFormatterAliases;
use App\Golem\GolemResponse;
use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Helper\MbtiHelper;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiTestRepository;
use App\Handler\QuickReply\QuickReplyDomainAliases;

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
        $test = $this->mbtiHelper->startTest($user);

        if ($test->getStep() > 1) {
            $message = [
                'type' => MessageFormatterAliases::QUICK_REPLY,
                'text' => $this->translator->trans('test_already_started', [], null, $user->getLocale()),
                'quick_replies' => [
                    [
                        'title' => $this->translator->trans('reset_test', [], 'mbti', $user->getLocale()),
                        'payload' => \json_encode([
                            'domain' => QuickReplyDomainAliases::MBTI_DOMAIN,
                            'type' => 'reset-test',
                        ]),
                    ],
                    [
                        'title' => $this->translator->trans('resume_test', [], 'mbti', $user->getLocale()),
                        'payload' => \json_encode([
                            'domain' => QuickReplyDomainAliases::MBTI_DOMAIN,
                            'type' => 'resume-test',
                        ]),
                    ],
                ],
            ];
        } else {
            $questions = $this->mbtiHelper->getNextQuestion($test);
            $message = $this->mbtiHelper->prepareQuestion($questions, $user);
        }

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
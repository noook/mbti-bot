<?php

namespace App\Handler\QuickReply;

use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\MessageFormatterCollection;
use App\Entity\FacebookUser;
use App\Helper\MbtiHelper;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiTestRepository;
use App\Formatter\MessageFormatterAliases;

class MbtiQuickReply implements QuickReplyDomainInterface
{
    private $mbtiHelper;
    private $messageFormatterCollection;
    private $messenger;
    private $facebookUserRepository;
    private $mbtiTestRepository;
    private $translator;

    public function __construct(
        MbtiHelper $mbtiHelper,
        MessageFormatterCollection $messageFormatterCollection,
        MessengerApi $messenger,
        FacebookUserRepository $facebookUserRepository,
        MbtiTestRepository $mbtiTestRepository,
        TranslatorInterface $translator
    )
    {
        $this->mbtiHelper = $mbtiHelper;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->messenger = $messenger;
        $this->facebookUserRepository = $facebookUserRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->translator = $translator;
    }

    public function getAlias(): string
    {
        return QuickReplyDomainAliases::MBTI_DOMAIN;
    }

    private function sendQuestions(array $questions, FacebookUser $user)
    {
        foreach ($questions as $message) {
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

    public function handleReply(MessengerRequestMessage $message)
    {
        $quickReply = \json_decode($message->getQuickReply(), true);

        switch ($quickReply['type']) {
            case 'answer':
                return $this->answer($message, $quickReply);
            case 'start-test':
                return $this->startTest($message, $quickReply);
            case 'reset-test':
                return $this->resetTest($message, $quickReply);
            case 'resume-test':
                return $this->resumeTest($message, $quickReply);
            
            default:
                break;
        }
    }

    private function answer(MessengerRequestMessage $message, array $quickReply)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $test = $this->mbtiTestRepository->findOneBy(['user' => $user]);
    
        if ($quickReply['step'] !== $test->getStep()) {
            return;
        }

        $next = $this->mbtiHelper->answerQuestion($message->getSender(), $quickReply);

        if (null === $next) {
            return $this->endTest($user);
        }

        $this->sendQuestions([$next], $user);
    }

    private function endTest(FacebookUser $user)
    {
        $messages = [];
        $test = $this->mbtiTestRepository->findLatestCompleted($user);
        $type = $test->getResult();
        $alias = $this->translator->trans('type_aka.' . $type, [], 'mbti', $user->getLocale());
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('end_result', ['{TYPE}' => $type], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('type_aka.aka_base', ['{alias}' => $alias], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('summaries.' . $type, [], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('detail_link', ['{type}' => strtolower($type)], 'mbti', $user->getLocale()),
        ];

        $this->sendQuestions($messages, $user);
    }

    private function startTest(MessengerRequestMessage $message, array $quickReply)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $test = $this->mbtiHelper->startTest($user);
        $questions = $this->mbtiHelper->getNextQuestion($test);
        $messages[] = $this->mbtiHelper->prepareQuestion($questions, $user);

        $this->sendQuestions($messages, $user);
    }

    private function resumeTest(MessengerRequestMessage $message, array $quickReply)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $test = $this->mbtiTestRepository->currentTest($user);
        $questions = $this->mbtiHelper->getNextQuestion($test);
        $messages[] = $this->mbtiHelper->prepareQuestion($questions, $user);

        $this->sendQuestions($messages, $user);
    }

    private function resetTest(MessengerRequestMessage $message, array $quickReply)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $this->mbtiTestRepository->deleteCurrent($user);

        $this->startTest($message, $quickReply);
    }
}
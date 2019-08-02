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

    public function handleReply(MessengerRequestMessage $message)
    {
        $quickReply = \json_decode($message->getQuickReply(), true);

        switch ($quickReply['type']) {
            case 'answer':
                return $this->answer($message, $quickReply);
            
            default:
                break;
        }
    }

    private function answer(MessengerRequestMessage $message, array $quickReply)
    {
        $next = $this->mbtiHelper->answerQuestion($message->getSender(), $quickReply['value']);
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);

        if (null === $next) {
            return $this->endTest($user);
        }

        $element = $this
            ->messageFormatterCollection
            ->get($next['type'])
            ->format($next);
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

    private function endTest(FacebookUser $user)
    {
        $messages = [];
        $test = $this->mbtiTestRepository->findLatestCompleted($user);
        $type = $test->getResult();
        $alias = $this->translator->trans('type_aka.' . $type, [], 'mbti', $user->getLocale());
        $messages[] = [
            'text' => $this->translator->trans('end_result', ['{TYPE}' => $type], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'text' => $this->translator->trans('type_aka.aka_base', ['{alias}' => $alias], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'text' => $this->translator->trans('summaries.' . $type, [], 'mbti', $user->getLocale()),
        ];
        $messages[] = [
            'text' => $this->translator->trans('detail_link', ['{type}' => strtolower($type)], 'mbti', $user->getLocale()),
        ];

        foreach ($messages as $item) {
            $message = $this
                ->messageFormatterCollection
                ->get('text')
                ->format($item);
            $this
                ->messenger
                ->setRecipient($user->getFbid())
                ->setTyping('on');
            sleep(2);
            $this
                ->messenger
                ->sendMessage($message)
                ->setTyping('off');
        }
    }
}
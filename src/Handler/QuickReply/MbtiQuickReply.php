<?php

namespace App\Handler\QuickReply;

use App\Helper\MbtiHelper;
use App\Messenger\MessengerRequestMessage;
use App\Messenger\MessengerApi;
use App\Collection\MessageFormatterCollection;
use App\Repository\FacebookUserRepository;

class MbtiQuickReply implements QuickReplyDomainInterface
{
    private $mbtiHelper;
    private $messageFormatterCollection;
    private $messenger;
    private $facebookUserRepository;

    public function __construct(
        MbtiHelper $mbtiHelper,
        MessageFormatterCollection $messageFormatterCollection,
        MessengerApi $messenger,
        FacebookUserRepository $facebookUserRepository
    )
    {
        $this->mbtiHelper = $mbtiHelper;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->messenger = $messenger;
        $this->facebookUserRepository = $facebookUserRepository;
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
}
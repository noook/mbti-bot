<?php

namespace App\Messenger;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use FacebookMessengerSendApi\SendAPI;

class MessengerApi
{
    public $sendApi;

    public function __construct(ParameterBagInterface $param)
    {
        $this->sendApi = (new SendAPI())
            ->setAccessToken($param->get('messenger_api_token'));
    }

    public function setRecipient(string $fbid): self
    {
        $this->sendApi->setRecipientId($fbid);

        return $this;
    }

    public function setTyping($status): self
    {
        $this->sendApi->senderActions("typing_$status");

        return $this;
    }

    public function markSeen(): self
    {
        $this->sendApi->senderActions('mark_seen');

        return $this;
    }

    public function sendMessage($message): self
    {
        $this->sendApi->sendMessage($message);

        return $this;
    }
}
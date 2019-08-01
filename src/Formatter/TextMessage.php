<?php

namespace App\Formatter;

use App\Messenger\MessengerApi;
use FacebookMessengerSendApi\SendAPITransform;

class TextMessage implements MessageFormatterInterface
{
    private $messenger;

    public function __construct(MessengerApi $messenger)
    {
        $this->messenger = $messenger;
    }

    public function getAlias(): string
    {
        return MessageFormatterAliases::TEXT;
    }

    public function format(array $message): SendAPITransform
    {
        return $this
            ->messenger
            ->sendApi
            ->contentType
            ->text
            ->text($message['text']);
    }
}
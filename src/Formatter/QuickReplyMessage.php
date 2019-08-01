<?php

namespace App\Formatter;

use App\Messenger\MessengerApi;
use FacebookMessengerSendApi\SendAPITransform;

class QuickReplyMessage implements MessageFormatterInterface
{
    private $messenger;

    public function __construct(MessengerApi $messenger)
    {
        $this->messenger = $messenger;
    }

    public function getAlias(): string
    {
        return MessageFormatterAliases::QUICK_REPLY;
    }

    public function format(array $message): SendAPITransform
    {
        $element = $this
            ->messenger
            ->sendApi
            ->quickReplies
            ->text($message['text']);

        foreach ($message['quick_replies'] as $quickReply) {
            $element->addQuickReply(
                $this
                    ->messenger
                    ->sendApi
                    ->quickReply
                    ->contentType('text')
                    ->title($quickReply['title'])
                    ->payload($quickReply['payload'])
            );
        }

        return $element;
    }
}
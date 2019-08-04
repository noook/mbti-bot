<?php

namespace App\Handler\MessengerEvent;

use App\Messenger\MessengerRequestMessage;
use App\Collection\PostbackHandlerCollection;

class MessengerPostbackHandler implements MessengerEventHandlerInterface
{
    private $postbackHandlerCollection;

    public function __construct(PostbackHandlerCollection $postbackHandlerCollection)
    {
        $this->postbackHandlerCollection = $postbackHandlerCollection;
    }

    public function getAlias(): string
    {
        return MessengerHandlerEventAliases::POSTBACK_HANDLER;
    }

    /**
     * @todo Handle Golem request
     */
    public function handle(MessengerRequestMessage $message)
    {
        $postback = \json_decode($message->getPostback(), true);
        $this
            ->postbackHandlerCollection
            ->get($postback['domain'])
            ->handleReply($message);
    }
}
<?php

namespace App\Handler\MessengerEvent;

use App\Messenger\MessengerRequestMessage;
use App\Collection\QuickReplyHandlerCollection;

class MessengerQuickReplyHandler implements MessengerEventHandlerInterface
{
    private $quickReplyHandlerCollection;

    public function __construct(QuickReplyHandlerCollection $quickReplyHandlerCollection)
    {
        $this->quickReplyHandlerCollection = $quickReplyHandlerCollection;
    }

    public function getAlias(): string
    {
        return MessengerHandlerEventAliases::QUICK_REPLY_HANDLER;
    }

    /**
     * @todo Handle Golem request
     */
    public function handle(MessengerRequestMessage $message)
    {
        $quickReply = \json_decode($message->getQuickReply(), true);
        $this
            ->quickReplyHandlerCollection
            ->get($quickReply['domain'])
            ->handleReply($message);
    }
}
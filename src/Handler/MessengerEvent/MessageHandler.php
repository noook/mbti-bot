<?php

namespace App\Handler\MessengerEvent;

use App\Golem\GolemRequestFactory;
use App\Messenger\MessengerRequestMessage;

class MessageHandler implements MessengerEventHandlerInterface
{
    private $golemRequestFactory;

    public function __construct(GolemRequestFactory $golemRequestFactory)
    {
        $this->golemRequestFactory = $golemRequestFactory;
    }
    public function getAlias(): string
    {
        return MessengerHandlerEventAliases::MESSAGE_HANDLER;
    }

    /**
     * @todo Handle Golem request
     */
    public function handle(MessengerRequestMessage $message)
    {
        $golemRequest = $this->golemRequestFactory->create();
    }
}
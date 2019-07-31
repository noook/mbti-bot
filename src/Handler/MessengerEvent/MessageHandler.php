<?php

namespace App\Handler\MessengerEvent;

use App\Collection\ContextHandlerCollection;
use App\Golem\GolemManager;
use App\Messenger\MessengerRequestMessage;

class MessageHandler implements MessengerEventHandlerInterface
{
    private $golemManager;
    private $contextHandlerCollection;

    public function __construct(GolemManager $golemManager, ContextHandlerCollection $contextHandlerCollection)
    {
        $this->golemManager = $golemManager;
        $this->contextHandlerCollection = $contextHandlerCollection;
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
        $golemRequest = $this->golemManager->createRequest(['text' => $message->getText()]);
        $golemResponse = $this->golemManager->makeRequest($golemRequest);

        if ($this->contextHandlerCollection->has($golemResponse->getCall()->getContextId())) {
            $this
                ->contextHandlerCollection
                ->get($golemResponse->getCall()->getContextId())
                ->handleResponse($message, $golemResponse);
        }
    }
}
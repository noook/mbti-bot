<?php

namespace App\Handler\Context;

use App\Collection\InteractionHandlerCollection;
use App\Golem\GolemResponse;
use App\Messenger\MessengerRequestMessage;

class MbtiContextHandler implements ContextHandlerInterface
{
    private $interactionHandlerCollection;

    public function __construct(InteractionHandlerCollection $interactionHandlerCollection)
    {
        $this->interactionHandlerCollection = $interactionHandlerCollection;
    }

    public function getAlias(): string
    {
        return ContextHandlerAliases::MBTI;
    }

    public function handleResponse(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse)
    {
        $interactionId = $golemResponse->getCall()->getInteractionId();

        if ($this->interactionHandlerCollection->has($interactionId)) {
            $this
                ->interactionHandlerCollection
                ->get($interactionId)
                ->handleInteraction($messengerRequest, $golemResponse);
        }
    }
}
<?php

namespace App\Handler\Context;

use App\Golem\GolemResponse;
use App\Collection\InteractionHandlerCollection;

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

    public function handleResponse(GolemResponse $golemResponse)
    {
        $interactionId = $golemResponse->getCall()->getInteractionId();

        if ($this->interactionHandlerCollection->has($interactionId)) {
            $this
                ->interactionHandlerCollection
                ->get($interactionId)
                ->handleInteraction($golemResponse);
        }
    }
}
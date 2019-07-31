<?php

namespace App\Handler\Context;

use App\Golem\GolemResponse;
use App\Collection\InteractionHandlerCollection;

class SmallTalkContextHandler implements ContextHandlerInterface
{
    private $interactionHandlerCollection;

    public function __construct(InteractionHandlerCollection $interactionHandlerCollection)
    {
        $this->interactionHandlerCollection = $interactionHandlerCollection;
    }

    public function getAlias(): string
    {
        return ContextHandlerAliases::SMALL_TALK;
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
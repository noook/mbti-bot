<?php

namespace App\Handler\Interaction\SmallTalkContext;

use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Golem\GolemResponse;

class SmallTalkInteractionHandler implements InteractionHandlerInterface
{
    public function getAlias(): string
    {
        return InteractionHandlerAliases::SMALL_TALK;
    }

    public function handleInteraction(GolemResponse $golemResponse)
    {
        dd('reached interaction ' . $this->getAlias());
    }
}
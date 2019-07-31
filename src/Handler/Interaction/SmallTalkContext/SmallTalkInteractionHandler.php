<?php

namespace App\Handler\Interaction\SmallTalkContext;

use App\Golem\GolemResponse;
use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Messenger\MessengerRequestMessage;

class SmallTalkInteractionHandler implements InteractionHandlerInterface
{
    public function getAlias(): string
    {
        return InteractionHandlerAliases::SMALL_TALK;
    }

    public function handleInteraction(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse)
    {
        dd('reached interaction ' . $this->getAlias());
    }
}
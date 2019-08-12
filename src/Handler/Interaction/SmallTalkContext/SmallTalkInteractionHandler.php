<?php

namespace App\Handler\Interaction\SmallTalkContext;

use App\Golem\GolemResponse;
use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Helper\BasicMessages;
use App\Messenger\MessengerRequestMessage;

class SmallTalkInteractionHandler implements InteractionHandlerInterface
{
    private $basicMessageHelper;

    public function __construct(BasicMessages $basicMessageHelper)
    {
        $this->basicMessageHelper = $basicMessageHelper;        
    }

    public function getAlias(): string
    {
        return InteractionHandlerAliases::SMALL_TALK;
    }

    public function handleInteraction(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse)
    {
        $this->basicMessageHelper->greet($messengerRequest);
    }
}
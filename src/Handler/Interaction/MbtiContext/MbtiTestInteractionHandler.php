<?php

namespace App\Handler\Interaction\MbtiContext;

use App\Handler\Interaction\InteractionHandlerInterface;
use App\Handler\Interaction\InteractionHandlerAliases;
use App\Golem\GolemResponse;

class MbtiTestInteractionHandler implements InteractionHandlerInterface
{
    public function getAlias(): string
    {
        return InteractionHandlerAliases::MBTI_TEST;
    }

    public function handleInteraction(GolemResponse $golemResponse)
    {
        dd('reached interaction ' . $this->getAlias());
    }
}
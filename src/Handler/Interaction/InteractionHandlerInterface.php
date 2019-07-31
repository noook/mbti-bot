<?php

namespace App\Handler\Interaction;

use App\Golem\GolemResponse;

interface InteractionHandlerInterface
{
    public function getAlias(): string;

    public function handleInteraction(GolemResponse $golemResponse);
}
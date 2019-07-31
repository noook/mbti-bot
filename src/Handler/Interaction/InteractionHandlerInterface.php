<?php

namespace App\Handler\Interaction;

use App\Golem\GolemResponse;
use App\Messenger\MessengerRequestMessage;

interface InteractionHandlerInterface
{
    public function getAlias(): string;

    public function handleInteraction(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse);
}
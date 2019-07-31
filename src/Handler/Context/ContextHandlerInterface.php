<?php

namespace App\Handler\Context;

use App\Golem\GolemResponse;
use App\Messenger\MessengerRequestMessage;

interface ContextHandlerInterface
{
    public function getAlias(): string;

    public function handleResponse(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse);
}
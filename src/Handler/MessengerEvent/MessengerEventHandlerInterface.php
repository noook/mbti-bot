<?php

namespace App\Handler\MessengerEvent;

use App\Messenger\MessengerRequestMessage;

interface MessengerEventHandlerInterface
{
    public function getAlias(): string;

    public function handle(MessengerRequestMessage $message);
}

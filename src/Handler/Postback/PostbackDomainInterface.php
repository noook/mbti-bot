<?php

namespace App\Handler\Postback;

use App\Messenger\MessengerRequestMessage;

interface PostbackDomainInterface
{
    public function getAlias(): string;

    public function handleReply(MessengerRequestMessage $message);
}
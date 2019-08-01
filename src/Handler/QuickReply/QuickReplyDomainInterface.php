<?php

namespace App\Handler\QuickReply;

use App\Messenger\MessengerRequestMessage;

interface QuickReplyDomainInterface
{
    public function getAlias(): string;

    public function handleReply(MessengerRequestMessage $quickReply);
}
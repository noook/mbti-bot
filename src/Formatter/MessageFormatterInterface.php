<?php

namespace App\Formatter;

use FacebookMessengerSendApi\SendAPITransform;

interface MessageFormatterInterface
{
    public function getAlias(): string;

    public function format(array $message): SendAPITransform;
}
<?php

namespace App\Handler\Context;

use App\Golem\GolemResponse;

class MbtiContextHandler implements ContextHandlerInterface
{
    public function getAlias(): string
    {
        return ContextHandlerAliases::MBTI;
    }

    public function handleResponse(GolemResponse $golemResponse)
    {
        dd('Reached good context handler');
    }
}
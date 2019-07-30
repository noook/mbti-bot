<?php

namespace App\Handler\Context;

use App\Golem\GolemResponse;

interface ContextHandlerInterface
{
    public function getAlias(): string;

    public function handleResponse(GolemResponse $golemResponse);
}
<?php

namespace App\Golem;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GolemHandler
{
    private $token;

    public function __construct(ParameterBagInterface $params)
    {
        $this->token = $params->get('golem_token');
    }
}
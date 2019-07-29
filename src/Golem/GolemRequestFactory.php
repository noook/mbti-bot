<?php

namespace App\Golem;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GolemRequestFactory
{
    private $defaultLang;

    public function __construct(ParameterBagInterface $params)
    {
        $this->defaultLang = $params->get('default_language');        
    }

    public function create(array $options = []): GolemRequest
    {
        $options = array_merge(['language' => $this->defaultLang], $options);

        return new GolemRequest($options);
    }
}
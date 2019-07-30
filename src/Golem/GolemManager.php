<?php

namespace App\Golem;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GolemManager
{
    private $defaultLang;
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->defaultLang = $params->get('default_language');        
    }

    public function createRequest(array $options = []): GolemRequest
    {
        $options = array_merge(['language' => $this->defaultLang], $options);

        return new GolemRequest($options);
    }

    public function makeRequest(GolemRequest $golemRequest): GolemResponse  
    {
        $data = array_merge([
            'token' => $this->params->get('golem_token'),
        ], $golemRequest->getParams());

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => \json_encode($data),
            ],
        ];

        $context  = stream_context_create($opts);

        $result = file_get_contents($this->params->get('golem_url'), false, $context);
        $result = \json_decode($result, true);
        $result['request'] = $golemRequest->getParams();

        return new GolemResponse($result);
    }
}
<?php

namespace App\Golem;

class GolemRequest
{
    const DEFAULT_OPTIONS =  [
        'type' => 'request',
        'text' => '',
        'language' => null,
        'labelling' => true,
        'parameters_detail' => false,
        'disable_verbose' => false,
        'multiple_interaction_search' => false,
        'conversation_mode' => false,
        'explanation' => true,
    ];

    private $params;

    public function __construct(array $options = [])
    {
        foreach (self::DEFAULT_OPTIONS as $key => $default) {
            $this->params[$key] = $options[$key] ??  $default;
        }
    }
}
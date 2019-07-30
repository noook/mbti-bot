<?php

namespace App\Golem\Components;

class GolemCall
{
    private $interactionId;
    private $contextId;
    private $parameters;
    private $incomplete;


    public function __construct(array $data)
    {
        $this->interactionId = $data['id_interaction'];
        $this->contextId = $data['id_context'];
        $this->parameters = $data['parameters'];
        $this->incomplete = $data['incomplete'];
    }

    public function getInteractionId(): string
    {
        return $this->interactionId;
    }

    public function getContextId(): string
    {
        return $this->contextId;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isIncomplete(): bool
    {
        return $this->incomplete;
    }
}
<?php

namespace App\Golem\Components;

class GolemLabel
{
    private $type;
    private $expression;
    private $archetype;
    private $usedParameter;
    private $interactionId;
    private $callIdx;

    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->expression = $data['expression'];
        $this->archetype = $data['archetype'];
        $this->usedParameter = $data['used_parameter'];
        $this->interactionId = $data['interaction_id'];
        $this->callIdx = $data['call_idx'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getArchetype(): string
    {
        return $this->archetype;
    }

    public function getUsedParameter(): string
    {
        return $this->usedParameter;
    }

    public function getInteractionId(): string
    {
        return $this->interactionId;
    }

    public function getCallIdx(): int
    {
        return $this->callIdx;
    }
}
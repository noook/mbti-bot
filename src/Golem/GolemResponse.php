<?php

namespace App\Golem;

use App\Golem\Components\GolemCall;
use App\Golem\Components\GolemLabel;

class GolemResponse
{
    private $request;
    private $type;
    private $call;
    private $language;
    private $text;
    private $id;
    private $timeAi;
    private $timeTotal;
    private $labels;
    private $verboseAvailableInteractions;
    private $helperMessage;

    public function __construct(array $data)
    {
        $this->request = $data['request'];
        $this->type = $data['type'];
        $this->call = isset($data['call']) ? new GolemCall($data['call']) : null;
        $this->language = $data['request_language'];
        $this->text = $data['request_text'];
        $this->id = $data['id_request'];
        $this->timeAi = $data['time_ai'];
        $this->timeTotal = $data['time_total'];
        $this->labels = array_map(function(array $label) {
            return new GolemLabel($label);
        }, $data['labels']);
        $this->verboseAvailableInteractions = $data['verbose_available_interactions'] ?? null;
        $this->helperMessage = $data['helper_message'] ?? null;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCall(): ?GolemCall
    {
        return $this->call;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTimeAi(): string
    {
        return $this->timeAi;
    }

    public function getTimeTotal(): string
    {
        return $this->timeTotal;
    }

    /**
     * @return GolemLabel[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getVerbose(): array
    {
        return $this->verboseAvailableInteractions;
    }

    public function getHelperMessage(): ?string
    {
        return $this->helperMessage;
    }
}
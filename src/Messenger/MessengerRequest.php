<?php

namespace App\Messenger;

class MessengerRequest
{
    private $object;
    private $entries;

    public function __construct(string $json)
    {
        $this->entries = [];
        $data = \json_decode($json, true);
        $this->object = $data['object'];
        foreach ($data['entry'] as $entry) {
            $this->entries[] = new MessengerRequestMessage($entry);
        }
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function addEntry(MessengerRequestMessage $message): self
    {
        $this->entries[] = $message;

        return $this;
    }

    public function deleteEntry(MessengerRequestMessage $message): self
    {
        $key = array_search($message->getId(), array_map(function (MessengerRequestMessage $item) {
            return $item->getId();
        }, $this->entries));

        if (false !== $key) {
            array_splice($this->entries, $key, 1);
        }

        return $this;
    }
}

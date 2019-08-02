<?php

namespace App\Messenger;

class MessengerRequestMessage
{
    private $id;
    private $eventTime;
    private $sender;
    private $recipient;
    private $timestamp;
    private $mid;
    private $seq;
    private $text;
    private $postback;
    private $quickReply;
    private $type;

    public function __construct(array $entry)
    {
        $message = $entry['messaging'][0];
        $this->setId($entry['id']);
        $this->setEventTime((int) $entry['time']);
        $this->setSender($message['sender']);
        $this->setRecipient($message['recipient']);
        $this->setTimestamp((int) $message['timestamp']);
        if (isset($message['message'])) {
            $this->setMid($message['message']['mid']);
            $this->setSeq($message['message']['seq'] ?? null);
            $this->setText($message['message']['text']);
            $this->type = 'message';
        }
        if (isset($message['postback']['payload'])) {
            $this->type = 'postback';
            $this->setPostback($message['postback']['payload']);
        }

        if (isset($message['message']['quick_reply'])) {
            $this->type = 'quick_reply';
            $this->setQuickReply($message['message']['quick_reply']['payload']);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEventTime(): int
    {
        return $this->eventTime;
    }

    public function setEventTime(int $timestamp): self
    {
        $this->eventTime = $timestamp;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender($sender): self
    {
        $this->sender = $sender['id'];

        return $this;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient($recipient): self
    {
        $this->recipient = $recipient['id'];

        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getMid(): ?string
    {
        return $this->mid;
    }

    public function setMid(string $mid): self
    {
        $this->mid = $mid;

        return $this;
    }

    public function getSeq(): ?int
    {
        return $this->seq;
    }

    public function setSeq(?int $seq): self
    {
        $this->seq = $seq;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of postback.
     */
    public function getPostback()
    {
        return $this->postback;
    }

    /**
     * Set the value of postback.
     *
     * @return self
     */
    public function setPostback($postback)
    {
        $this->postback = $postback;

        return $this;
    }

    public function getQuickReply(): ?string
    {
        return $this->quickReply;
    }

    public function setQuickReply(?string $text): self
    {
        $this->quickReply = $text;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}

<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Formatter\MessageFormatterInterface;

class MessageFormatterCollection
{
    private $collection;

    public function __construct()
    {
        $this->collection = [];
    }

    public function has(string $alias): bool
    {
        return isset($this->collection[$alias]);
    }

    public function add(MessageFormatterInterface $messageFormatter)
    {
        $this->collection[$messageFormatter->getAlias()] = $messageFormatter;
    }

    public function get(string $alias): MessageFormatterInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException;
        }
        
        return $this->collection[$alias];
    }
}
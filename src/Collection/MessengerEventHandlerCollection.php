<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Handler\MessengerEvent\MessengerEventHandlerInterface;

class MessengerEventHandlerCollection
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

    public function add(MessengerEventHandlerInterface $messengerEventHandler)
    {
        $this->collection[$messengerEventHandler->getAlias()] = $messengerEventHandler;
    }

    public function get(string $alias): MessengerEventHandlerInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException;
        }
        
        return $this->collection[$alias];
    }
}
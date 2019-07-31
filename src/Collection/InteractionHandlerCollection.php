<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Handler\Interaction\InteractionHandlerInterface;

class InteractionHandlerCollection
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

    public function add(InteractionHandlerInterface $interactionHandler)
    {
        $this->collection[$interactionHandler->getAlias()] = $interactionHandler;
    }

    public function get(string $alias): InteractionHandlerInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException;
        }
        
        return $this->collection[$alias];
    }
}
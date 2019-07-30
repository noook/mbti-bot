<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Handler\Context\ContextHandlerInterface;

class ContextHandlerCollection
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

    public function add(ContextHandlerInterface $contextHandlerInterface)
    {
        $this->collection[$contextHandlerInterface->getAlias()] = $contextHandlerInterface;
    }

    public function get(string $alias): ContextHandlerInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException();
        }
        
        return $this->collection[$alias];
    }
}
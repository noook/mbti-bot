<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Handler\Postback\PostbackDomainInterface;

class PostbackHandlerCollection
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

    public function add(PostbackDomainInterface $postbackHandler)
    {
        $this->collection[$postbackHandler->getAlias()] = $postbackHandler;
    }

    public function get(string $alias): PostbackDomainInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException();
        }
        
        return $this->collection[$alias];
    }
}
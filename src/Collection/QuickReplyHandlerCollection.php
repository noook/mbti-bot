<?php

namespace App\Collection;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Handler\QuickReply\QuickReplyDomainInterface;

class QuickReplyHandlerCollection
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

    public function add(QuickReplyDomainInterface $quickReplyHandler)
    {
        $this->collection[$quickReplyHandler->getAlias()] = $quickReplyHandler;
    }

    public function get(string $alias): QuickReplyDomainInterface
    {
        if (!isset($this->collection[$alias])) {
            throw new NotFoundHttpException();
        }
        
        return $this->collection[$alias];
    }
}
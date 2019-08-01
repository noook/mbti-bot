<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use App\Collection\QuickReplyHandlerCollection;

class QuickReplyHandlerPass implements CompilerPassInterface
{
    const COLLECTION_NAME = QuickReplyHandlerCollection::class;

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::COLLECTION_NAME)) {
            return;
        }

        $definition = $container->findDefinition(self::COLLECTION_NAME);
        $taggedServices = $container->findTaggedServiceIds('app.quick_reply');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
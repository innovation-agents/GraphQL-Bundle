<?php

namespace InnovationAgents\GraphQLBundle\CompilerPass;


use InnovationAgents\GraphQLBundle\Schema\Resolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResolverCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Resolver::class)) {
            return;
        }

        $definition = $container->findDefinition(Resolver::class);

        $taggedServices = $container->findTaggedServiceIds('ia_graphql_resolver');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addResolver', [
                new Reference($id)
            ]);
        }
    }
}
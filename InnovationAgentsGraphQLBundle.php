<?php

namespace InnovationAgents\GraphQLBundle;


use InnovationAgents\GraphQLBundle\CompilerPass\ResolverCompilerPass;
use InnovationAgents\GraphQLBundle\Schema\ResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InnovationAgentsGraphQLBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ResolverInterface::class)
            ->addTag('ia_graphql_resolver')
        ;

        $container->addCompilerPass(new ResolverCompilerPass());
    }
}
<?php

namespace InnovationAgents\GraphQLBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('innovation_agents_graph_ql');

        $rootNode
            ->children()
                ->scalarNode('graphql_schema_file')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
<?php

namespace InnovationAgents\GraphQLBundle\Mutation;


use GraphQL\Language\AST\Node;
use PM\DataHubBundle\GraphQL\Schema\ResolverInterface;

interface ScalarResolverInterface extends ResolverInterface
{
    public function serialize($value);
    public function parseValue($value);
    public function parseLiteral(Node $value);
    public function resolve(array $arguments, $value=null);
}
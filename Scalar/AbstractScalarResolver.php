<?php

namespace InnovationAgents\GraphQLBundle\Mutation;


use GraphQL\Language\AST\Node;

abstract class AbstractScalarResolver implements ScalarResolverInterface
{
    public function getType(): string
    {
        return 'scalar';
    }

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral(Node $value)
    {
        return $value->value;
    }
}
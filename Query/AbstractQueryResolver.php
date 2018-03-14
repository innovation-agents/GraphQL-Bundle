<?php

namespace InnovationAgents\GraphQLBundle\Query;


abstract class AbstractQueryResolver implements QueryResolverInterface
{
    public function getType(): string
    {
        return 'query';
    }

    public function getFieldSelectionDepth(): int
    {
        return 2;
    }
}
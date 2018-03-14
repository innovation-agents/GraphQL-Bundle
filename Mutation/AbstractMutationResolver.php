<?php

namespace InnovationAgents\GraphQLBundle\Mutation;


abstract class AbstractMutationResolver implements MutationResolverInterface
{
    public function getType(): string
    {
        return 'mutation';
    }

    public function getFieldSelectionDepth(): int
    {
        return 1;
    }
}
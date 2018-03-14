<?php

namespace InnovationAgents\GraphQLBundle\Mutation;


use PM\DataHubBundle\GraphQL\Schema\ResolverInterface;

interface MutationResolverInterface extends ResolverInterface
{
    public function resolve(string $mutation, array $arguments, array $fields);
    public function getFieldSelectionDepth(): int;
}
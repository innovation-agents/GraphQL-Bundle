<?php

namespace InnovationAgents\GraphQLBundle\Mutation;


interface QueryResolverInterface extends ResolverInterface
{
    /**
     * @param string $fieldName
     * @param array $arguments
     * @param string[] $fields
     * @param mixed|null $value
     * @return mixed
     */
    public function resolve(string $fieldName, array $arguments, array $fields, $value=null);

    public function getFieldSelectionDepth(): int;
}
<?php

namespace InnovationAgents\GraphQLBundle\Schema;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use InnovationAgents\GraphQLBundle\Mutation\MutationResolverInterface;
use InnovationAgents\GraphQLBundle\Mutation\QueryResolverInterface;
use InnovationAgents\GraphQLBundle\Mutation\ScalarResolverInterface;

class Resolver
{
    /** @var QueryResolverInterface[] */
    private $queryResolvers;

    /** @var MutationResolverInterface[] */
    private $mutationResolvers;

    /** @var ScalarResolverInterface[] */
    private $scalarResolvers;

    /** @var string[] */
    private $resolvedTypes;

    public function __construct()
    {
        $this->queryResolvers = [];
        $this->mutationResolvers = [];
        $this->scalarResolvers = [];

        $this->resolvedTypes = [];
    }

    public function addResolver(ResolverInterface $resolver)
    {
        $name = $resolver->getName();
        $type = $resolver->getType();

        switch($type) {
            case 'query':
                $this->queryResolvers[$name] = $resolver;
                break;
            case 'mutation':
                $this->mutationResolvers[$name] = $resolver;
                break;
            case 'scalar':
                $this->scalarResolvers[$name] = $resolver;
                break;
        }

        if(!in_array($name, $this->resolvedTypes)) {
            $this->resolvedTypes[] = $name;
        }
    }

    public function getFieldResolver(): callable
    {
        $fieldResolver = function($value, $arguments, $context, ResolveInfo $info) {
            $returnType = $info->returnType;

            if($returnType instanceof ListOfType) {
                $returnType = $returnType->ofType;
            }

            $name = $returnType->name;

            if(!in_array($name, $this->resolvedTypes)) {
                return $this->defaultResolver($info->fieldName, $value, $arguments, $context);
            }

            $operation = strtolower($info->operation->operation);

            if(array_key_exists($name, $this->scalarResolvers) && $operation === 'query') {
                return $this->scalarResolvers[$name]->resolve($arguments, $value);
            }

            $fieldSelectionDepth = $this->getResolverByType($operation, $name)->getFieldSelectionDepth();

            $fields = $returnType instanceof ObjectType && $fieldSelectionDepth > 0
                      ? $info->getFieldSelection($fieldSelectionDepth)
                      : [];

            if($operation === 'query') {
                return $this->queryResolvers[$name]->resolve($info->fieldName, $arguments, $fields, $value);
            }

            if($operation === 'mutation') {
                return $this->mutationResolvers[$name]->resolve($info->fieldName, $arguments, $fields);
            }

            throw new \RuntimeException('and now ???');
        };

        $fieldResolver->bindTo($this);

        return $fieldResolver;
    }

    /**
     * @param string $type
     * @param string $name
     * @return QueryResolverInterface | ScalarResolverInterface | MutationResolverInterface
     */
    private function getResolverByType(string $type, string $name)
    {
        $property = lcfirst($type).'Resolvers';

        return array_key_exists($name, $this->$property) ? $this->$property[$name] : null;
    }

    public function getTypeConfigDecorator(): callable
    {
        $typeConfigDecorator = function($typeConfig) {
            $name = $typeConfig['name'];

            if(array_key_exists($name, $this->scalarResolvers)) {
                /** @var ScalarResolverInterface $resolver */
                $resolver = $this->scalarResolvers[$name];

                $typeConfig['serialize'] = function($value) use($resolver) {
                    return $resolver->serialize($value);
                };
                $typeConfig['parseValue'] = function($value) use($resolver) {
                    return $resolver->parseValue($value);
                };
                $typeConfig['parseLiteral'] = function($value) use($resolver) {
                    return $resolver->parseLiteral($value);
                };
            }

            return $typeConfig;
        };

        $typeConfigDecorator->bindTo($this);

        return $typeConfigDecorator;
    }

    private function defaultResolver(string $fieldName, $val, $args, $context)
    {
        $property = null;

        if (is_array($val) || $val instanceof \ArrayAccess) {
            if (isset($val[$fieldName])) {
                $property = $val[$fieldName];
            }
        } else if (is_object($val)) {
            if (isset($val->{$fieldName})) {
                $property = $val->{$fieldName};
            }
        }

        return $property instanceof \Closure ? $property($val, $args, $context) : $property;
    }

}
<?php

namespace InnovationAgents\GraphQLBundle\Schema;


interface ResolverInterface
{
    public function getName(): string;
    public function getType(): string;
}
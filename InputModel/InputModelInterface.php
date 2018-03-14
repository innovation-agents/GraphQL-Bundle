<?php

namespace InnovationAgents\GraphQLBundle\InputModel;

interface InputModelInterface
{
    public function getName(): string;
    public function setData(array $data): InputModelInterface;
    public function getData(): array;
}
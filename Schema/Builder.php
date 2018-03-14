<?php

namespace InnovationAgents\GraphQLBundle\Schema;


use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\Parser;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;

class Builder
{
    /** @var Resolver */
    private $schemaResolver;

    public function __construct(Resolver $schemaResolver)
    {
        $this->schemaResolver = $schemaResolver;
    }

    public function buildFromFile(string $path)
    {
        return $this->getParsedDocument($path);
    }

    public function getSerializedString(DocumentNode $document)
    {
        return serialize(AST::toArray($document));
    }

    public function buildFromSerializedString(string $serializedString)
    {
        return AST::fromArray(
            unserialize($serializedString)
        );
    }

    private function getParsedDocument(string $path)
    {
        if(!file_exists($path)) {
            throw new \RuntimeException(sprintf(
                'graphql schema file not found on location %s',
                $path
            ));
        }

        return Parser::parse(
            file_get_contents($path)
        );
    }

    public function buildSchema(Node $document)
    {
        return BuildSchema::build($document, $this->schemaResolver->getTypeConfigDecorator());
    }
}
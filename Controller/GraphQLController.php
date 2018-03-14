<?php

namespace InnovationAgents\GraphQLBundle\Controller;

use InnovationAgents\GraphQLBundle\Schema\Builder;
use InnovationAgents\GraphQLBundle\Schema\Resolver;
use InnovationAgents\GraphQLBundle\Server;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GraphQLController extends Controller
{
    /** @var Resolver */
    protected $resolver;

    /** @var Builder */
    protected $builder;

    /** @var string */
    protected $schemaFile;

    public function __construct(Resolver $resolver, Builder $builder, string $schemaFile)
    {
        $this->resolver = $resolver;
        $this->builder = $builder;
        $this->schemaFile = $schemaFile;
    }

    public function endpoint(Request $request)
    {
        $document = $this->builder->buildFromFile($this->schemaFile);
        $schema = $this->builder->buildSchema($document);

        $server = Server::create(
            $schema,
            $this->resolver->getFieldResolver(),
            $this->getParameter('kernel.debug'),
            $request->query->has('batch'),
            $request->query->has('rethrow')
        );

        $result = $server->handleRequest($request);
        return $result;
    }

    public function docs()
    {
        return $this->render('@InnovationAgentsGraphQL/docs.html.twig');
    }
}
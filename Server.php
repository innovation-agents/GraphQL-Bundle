<?php

namespace InnovationAgents\GraphQLBundle;


use GraphQL\Error\Debug;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Schema;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Server
{
    /** @var StandardServer */
    private $server;

    private $useQueryBatching;

    public function __construct(StandardServer $server, bool $queryBatching)
    {
        $this->useQueryBatching = $queryBatching;
        $this->server = $server;

        return $this;
    }

    public static function create(
        Schema $schema,
        callable $fieldResolver,
        bool $debug,
        bool $useQueryBatching=false,
        bool $rethrowInternalException=false
    ): Server
    {
        $debugFlags = false;

        if($debug && $rethrowInternalException) {
            $debugFlags = Debug::INCLUDE_TRACE | Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS;
        }
        elseif($debug && !$rethrowInternalException) {
            $debugFlags = Debug::INCLUDE_TRACE | Debug::INCLUDE_DEBUG_MESSAGE;
        }

        $config = new ServerConfig();
        $config
            ->setSchema($schema)
            ->setFieldResolver($fieldResolver)
            ->setDebug($debugFlags)
            ->setQueryBatching($useQueryBatching)
        ;

        return new static(
            new StandardServer($config),
            $useQueryBatching
        );
    }

    public function handleRequest(Request $request)
    {
        $psr7Factory = new DiactorosFactory();
        $psr7Request = $psr7Factory->createRequest($request);
        $psr7Request = $psr7Request->withParsedBody(json_decode($request->getContent(), true));

        $result = $this->server->executePsrRequest($psr7Request);

        $hasErrors = false;
        if($this->useQueryBatching) {
            $result = array_map(function(ExecutionResult $executionResult) {
                return $executionResult->jsonSerialize();
            }, $result);
        }
        else {
            $hasErrors = sizeof($result->errors);
            $result = $result->jsonSerialize();
        }

        return new JsonResponse(
            $result,
            $hasErrors ? 500 : 200, [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}
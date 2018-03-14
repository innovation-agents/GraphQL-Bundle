# Innovation Agents GraphQL Bundle

// very early state ... handle with care :-)

Integrates the fantastic [webonyx/graphql-php](https://github.com/webonyx/graphql-php) Library into Symfony 4 and 
maps every GraphQL-Type into a single service class

## GraphQL? ... it's awesome

* http://olegilyenko.github.io/presentation-graphql-introduction/#/
* http://graphql.org/learn/
* https://plugins.jetbrains.com/plugin/8097-js-graphql (Jetbrains Integration)

## Installation

### 1. add with composer

    composer require innovation-agents/graphql-bundle
    
### 2. add to routing.yaml

    # /config/routes.yaml
    
    ia_graphql:
        resource: "@InnovationAgentsGraphQLBundle/Resources/config/routing.yml"
        prefix: /graphql
        
### 3. define path to schema file

    # /config/packages/ia_graphql.yaml
    
    innovation_agents_graph_ql:
      graphql_schema_file: '%kernel.root_dir%/../schema.graphqls'
      
### 4. write schema file
### 5. define services

Every GraphQL-Type has one specific service interface as well as an abstract class which handles a small boilerplate.
Simply extends e.g. the class _AbstractQueryResolver_ and implement your own logic. Due to Symfony's Autowiring, your 
service will be recognized automatically if you implement the ResolverInterface.

Also look at the [documentation of webonyx/graphql-php](https://webonyx.github.io/graphql-php/) for further information

Here a small example to resolve the current user ...

    class CurrentUserResolver extends AbstractQueryResolver
    {
        /** @var  TokenStorage */
        private $tokenStorage;
    
        public function __construct(TokenStorage $tokenStorage)
        {
            $this->tokenStorage = $tokenStorage;
        }
    
        public function getName(): string
        {
            return 'CurrentUser';
        }
    
        public function resolve(string $fieldName, array $arguments, array $fields, $value=null)
        {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
    
            $return = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ];
    
            return $return;
        }
    
    }
    
## Contributing

You are welcome :-) 
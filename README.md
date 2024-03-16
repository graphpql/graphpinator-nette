# Graphpinator Nette [![PHP](https://github.com/infinityloop-dev/graphpinator-nette/workflows/PHP/badge.svg?branch=master)](https://github.com/infinityloop-dev/graphpinator-nette/actions?query=workflow%3APHP) [![codecov](https://codecov.io/gh/infinityloop-dev/graphpinator-nette/branch/master/graph/badge.svg)](https://codecov.io/gh/infinityloop-dev/graphpinator-nette)

:zap::globe_with_meridians::zap: Graphpinator adapters and addons for Nette framework.

## Introduction

This package includes adapters for various Graphpinator functionalities and a SchemaPresenter, which returns a response with generated GraphQL type language document.

## Installation

Install package using composer

```composer require infinityloop-dev/graphpinator-nette```

## How to use

### ApiPresenter

Simple version of a presenter to execute Graphql API requests against a given schema. It can be extended to alter its functionality (for example by overriding the `getEnabledModules` function) or it can serve as an inspiration to include the functionality in your own presenters.

Presenter is enabled by creating a route:

```php
$router[] = new Route('/', [
    'module' => 'Graphpinator',
    'presenter' => 'Api',
]);
```

There needs to be a `Schema` and a `NetteCache` service available in your DI container so that it can be injected into the presenter.

```neon
# Automatically find and register all types and directives located in GraphQl namespace as services
search:
    graphql:
        in: '%appDir%/GraphQL'
        extends:
            - Graphpinator\Typesystem\Contract\NamedType
            - Graphpinator\Typesystem\Contract\Directive
services:
    # Register a NetteCache adapter
    - Graphpinator\Nette\NetteCache

    # The SimpleContainer is a container of Graphql types
    # It is automatically injected by all types and directives as Nette automatically detects a typehint in SimpleContainers contructor
    - Graphpinator\SimpleContainer

    # Any additional types must be also registred to become available in the type container
    - Graphpinator\ExtraTypes\EmailAddressType
    - Graphpinator\ExtraTypes\PhoneNumberType

    # Register a Schema
    - Graphpinator\Typesystem\Schema(
        @Graphpinator\SimpleContainer, # Container of types
        @App\GraphQL\Query, # Query type
        null, # Mutation type
        null # Subscription type
    )

   # Alternativelly you may use the named service and add a setup to the Schema service
   schema.public:
        factory: Graphpinator\Typesystem\Schema(
            @Graphpinator\SimpleContainer,
            @App\GraphQL\Query,
            @App\GraphQL\Mutation,
            null
        )
        setup:
            - setDescription("""
            My GraphQL API
            """)
```

### SchemaPresenter

Schema presenter contains two actions.
- actionHtml, which renders HTML page
- actionFile, which renders text file - file is sent to browser as an attachment, which tells the browser to show the download prompt

Action can be enabled using Router, here is the example which enables the HTML action on the `/schema.graphql` path.

```php
$router[] = new Route('/schema.graphql',
    'module' => 'Graphpinator',
    'presenter' => 'Schema',         
    'action' => 'html',
]);
```

There needs to be a `Schema` service available in your DI container so that it can be injected into the presenter.

### GraphiQLPresenter

Presenter which include [GraphiQL](https://github.com/graphql/graphiql/tree/main/packages/graphiql#readme), a graphical interface to interact with your schema.

Presenter is enabled by creating a route:

```php
$router[] = new Route('/graphiql',
    'module' => 'Graphpinator',
    'presenter' => 'GraphiQl',         
    'action' => 'default',
]);
```

It is also required to pass a location of your API endpoint, to which GraphiQL will connect to.

```neon
services:
    Graphpinator\Nette\GraphiQlPresenter(':Api:Graphql:default')
```

### Presenter Mapping

In order to use presenters, it is required to register the application-module to correct namespace.

```neon
application:
    mapping:
        Graphpinator: 'Graphpinator\Nette\*Presenter'
```

### Cyclic dependendencies

When using abstract types, the cyclic dependencies must be avoided using accessors. Nette makes it easy by automatically providing implementation for a accessor interface using a simple DI condifuration.
    
```php
interface SlideAccessor
{
    public function getSlideSingle() : SlideSingle;

    public function getSlideDouble() : SlideDouble;

    public function getSlideTriple() : SlideTriple;
}
```

```neon
services:
    - SlideAccessor(
        slideSingle: @SlideSingle
        slideDouble: @SlideDouble
        slideTriple: @SlideTriple
    )
```

This interface is than injected into the abstract type to break the dependency cycle.

### Adapters

- `\Graphpinator\Nette\TracyLogger`
    - Implements logger interface for logging in `\Graphpinator\Graphpinator`.
- `\Graphpinator\Nette\NetteRequestFactory`
    - Implements `RequestFactory` and enables direct creation of `\Graphpinator\Request\Request` from Nette HTTP abstraction.
- `\Graphpinator\Nette\FileProvider`
    - Implements `FileProvider` interface needed by `infinityloop-dev/graphpinator-upload` module.
- `\Graphpinator\Nette\NetteCache`
    - Adapter from Nette Caching to Psr CacheInterface needed by `infinityloop-dev/graphpinator-persisted-queries` module.

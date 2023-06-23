# Graphpinator Nette [![PHP](https://github.com/infinityloop-dev/graphpinator-nette/workflows/PHP/badge.svg?branch=master)](https://github.com/infinityloop-dev/graphpinator-nette/actions?query=workflow%3APHP) [![codecov](https://codecov.io/gh/infinityloop-dev/graphpinator-nette/branch/master/graph/badge.svg)](https://codecov.io/gh/infinityloop-dev/graphpinator-nette)

:zap::globe_with_meridians::zap: Graphpinator adapters and addons for Nette framework.

## Introduction

This package includes adapters for various Graphpinator functionalities and a SchemaPresenter, which returns a response with generated GraphQL type language document.

## Installation

Install package using composer

```composer require infinityloop-dev/graphpinator-nette```

## How to use

### Adapters

- `\Graphpinator\Nette\TracyLogger`
    - Implements logger interface for logging in `\Graphpinator\Graphpinator`.
- `\Graphpinator\Nette\NetteRequestFactory`
    - Implements `RequestFactory` and enables direct creation of `\Graphpinator\Request\Request` from Nette HTTP abstraction.
- `\Graphpinator\Nette\FileProvider`
    - Implements `FileProvider` interface needed by `infinityloop-dev/graphpinator-upload` module.
- `\Graphpinator\Nette\NetteCache`
    - Adapter from Nette Caching to Psr CacheInterface needed by `infinityloop-dev/graphpinator-persisted-queries` module.

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

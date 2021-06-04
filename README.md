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
    - Implements logger interface for loggin in `\Graphpinator\Graphpinator`.
- `\Graphpinator\Nette\FileProvider`
    - Implements FileProvider interface needed by `infinityloop-dev/graphpinator-upload` module.
- `\Graphpinator\Nette\NetteRequestFactory`
    - Implements RequestFactory and enables direct creation of `\Graphpinator\Request\Request` from Nette HTTP abstraction.

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

It is also required to register the application-module to correct namespace.

```neon
application:
    mapping:
        Graphpinator: 'Graphpinator\Nette\*Presenter'
```

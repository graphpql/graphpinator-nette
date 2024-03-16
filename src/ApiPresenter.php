<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

use \Nette\Application\Responses\TextResponse;

class ApiPresenter implements \Nette\Application\IPresenter
{
    private \Graphpinator\Graphpinator $graphpinator;

    public function __construct(
        \Graphpinator\Typesystem\Schema $schema,
        private \Nette\Http\IRequest $request,
        private \Nette\Http\IResponse $response,
        private \Graphpinator\Nette\NetteCache $cache,
        bool $debugMode = false,
    )
    {
        $this->graphpinator = new \Graphpinator\Graphpinator(
            $schema,
            $debugMode
                ? \Graphpinator\ErrorHandlingMode::OUTPUTABLE
                : \Graphpinator\ErrorHandlingMode::ALL,
            $this->getEnabledModules(),
            new \Graphpinator\Nette\TracyLogger(),
        );
    }

    public function run(\Nette\Application\Request $request) : \Nette\Application\Response
    {
        return match ($this->request->getMethod()) {
            'HEAD', 'OPTIONS' => $this->createPreflightResponse(),
            'GET', 'POST' => $this->createApiResponse(),
            default => throw new \Nette\Application\BadRequestException('Only HEAD, OPTIONS, GET, POST methods are supported.'),
        };
    }

    protected function getEnabledModules() : \Graphpinator\Module\ModuleSet
    {
        return new \Graphpinator\Module\ModuleSet([
            //new \Graphpinator\QueryCost\MaxDepthModule(15),
            new \Graphpinator\PersistedQueries\PersistedQueriesModule($schema, $this->cache),
        ]);
    }

    private function createPreflightResponse() : TextResponse
    {
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'HEAD, GET, POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $this->response->setHeader('Access-Control-Max-Age', '86400');

        return new TextResponse('OK');
    }

    private function createApiResponse() : TextResponse
    {
        return new TextResponse(
            $this->graphpinator->run(new \Graphpinator\Nette\NetteRequestFactory($this->request, false))->toString(),
        );
    }
}

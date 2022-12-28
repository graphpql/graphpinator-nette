<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

use \Infinityloop\Utils\Json;

final class NetteRequestFactory implements \Graphpinator\Request\RequestFactory
{
    use \Nette\SmartObject;

    public function __construct(
        private \Nette\Http\IRequest $request,
        private bool $strict = true,
    )
    {
    }

    public function create() : \Graphpinator\Request\Request
    {
        $method = $this->request->getMethod();

        if (!\in_array($method, ['GET', 'POST'], true)) {
            throw new \Graphpinator\Request\Exception\InvalidMethod();
        }

        $contentType = $this->request->getHeader('Content-Type');

        if (\is_string($contentType) && \str_starts_with($contentType, 'multipart/form-data')) {
            if ($method === 'POST' && \array_key_exists('operations', $this->request->getPost())) {
                return $this->applyJsonFactory(Json::fromString($this->request->getPost('operations')));
            }

            throw new \Graphpinator\Request\Exception\InvalidMultipartRequest();
        }

        switch ($contentType) {
            case 'application/graphql':
                return new \Graphpinator\Request\Request($this->request->getRawBody()
                    ?? '');
            case 'application/json':
                return $this->applyJsonFactory(Json::fromString($this->request->getRawBody()
                    ?? '{}'));
            default:
                $params = $this->request->getQuery();

                if (\array_key_exists('variables', $params)) {
                    $params['variables'] = Json::fromString($params['variables'])->toNative();
                }

                return $this->applyJsonFactory(Json::fromNative((object) $params));
        }
    }

    private function applyJsonFactory(Json $json) : \Graphpinator\Request\Request
    {
        $jsonFactory = new \Graphpinator\Request\JsonRequestFactory($json, $this->strict);

        return $jsonFactory->create();
    }
}

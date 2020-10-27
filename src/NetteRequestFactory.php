<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class NetteRequestFactory implements \Graphpinator\Request\RequestFactory
{
    use \Nette\SmartObject;

    private \Nette\Http\Request $request;
    private bool $strict;

    public function __construct(\Nette\Http\Request $request, bool $strict = true)
    {
        $this->request = $request;
        $this->strict = $strict;
    }

    public function create() : \Graphpinator\Request\Request
    {
        $method = $this->request->getMethod();

        if (!\in_array($method, ['GET', 'POST'], true)) {
            throw new \Graphpinator\Exception\Request\InvalidMethod();
        }

        $contentType = $this->request->getHeader('Content-Type');

        if (\is_string($contentType) && \str_starts_with($contentType, 'multipart/form-data')) {
            if ($method === 'POST' && \array_key_exists('operations', $this->request->getPost())) {
                return $this->applyJsonFactory(\Graphpinator\Json::fromString($this->request->getPost('operations')));
            }

            throw new \Graphpinator\Exception\Request\InvalidMultipartRequest();
        }

        switch ($contentType) {
            case 'application/graphql':
                return new \Graphpinator\Request\Request($this->request->getRawBody()
                    ?? '');
            case 'application/json':
                return $this->applyJsonFactory(\Graphpinator\Json::fromString($this->request->getRawBody()
                    ?? '{}'));
            default:
                $params = $this->request->getQuery();

                if (\array_key_exists('variables', $params)) {
                    $params['variables'] = \Graphpinator\Json::fromString($params['variables'])->toObject();
                }

                return $this->applyJsonFactory(\Graphpinator\Json::fromObject((object) $params));
        }
    }

    private function applyJsonFactory(\Graphpinator\Json $json) : \Graphpinator\Request\Request
    {
        $jsonFactory = new \Graphpinator\Request\JsonRequestFactory($json, $this->strict);

        return $jsonFactory->create();
    }
}

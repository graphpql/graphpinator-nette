<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class Request extends \Graphpinator\Request
{
    public static function fromNetteHttpRequest(\Nette\Http\Request $request, bool $strict = true) : \Graphpinator\Request
    {
        $method = $request->getMethod();

        if (!\in_array($method, ['GET', 'POST'], true)) {
            throw new \Graphpinator\Exception\Request\InvalidMethod();
        }

        $contentType = $request->getHeader('Content-Type');

        if (\is_string($contentType) && \str_starts_with($contentType, 'multipart/form-data')) {
            if ($method === 'POST' && \array_key_exists('operations', $request->getPost())) {
                return self::fromJson(\Graphpinator\Json::fromString($request->getPost('operations')), $strict);
            }

            throw new \Graphpinator\Exception\Request\InvalidMultipartRequest();
        }

        switch ($contentType) {
            case 'application/graphql':
                return new self($request->getRawBody()
                    ?? '');
            case 'application/json':
                return self::fromJson(\Graphpinator\Json::fromString($request->getRawBody()
                    ?? '{}'), $strict);
            default:
                $params = $request->getQuery();

                if (\array_key_exists('variables', $params)) {
                    $params['variables'] = \Graphpinator\Json::fromString($params['variables'])->toObject();
                }

                return self::fromJson(\Graphpinator\Json::fromObject((object) $params), $strict);
        }
    }
}

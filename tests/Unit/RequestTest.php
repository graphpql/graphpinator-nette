<?php

declare(strict_types = 1);

namespace Graphpinator\Nette\Tests\Unit;

final class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function testJsonBody() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            [],
            [],
            [],
            ['Content-Type' => 'application/json'],
            'GET',
            null,
            null,
            static function() {
                return '{"query":"query {}", "variables": {"var1":"varValue"}, "operationName": "opName"}';
            },
        );
        $request = (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testGraphqlBody() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            [],
            [],
            [],
            ['Content-Type' => 'application/graphql'],
            'GET',
            null,
            null,
            static function() {
                return 'query {}';
            },
        );
        $request = (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();

        self::assertSame('query {}', $request->getQuery());
        self::assertSame([], (array) $request->getVariables());
        self::assertNull($request->getOperationName());
    }

    public function testQueryParams() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript('/?query=query%20{}&variables={"var1":"varValue"}&operationName=opName'),
        );
        $request = (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testMultipart() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            ['map' => '[]', 'operations' => '{"query":"query {}", "variables":{"var1":"varValue"}, "operationName":"opName"}'],
            [],
            [],
            ['Content-Type' => 'multipart/form-data; boundary=--------boundary'],
            'POST',
        );
        $request = (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testInvalidMethod() : void
    {
        $this->expectException(\Graphpinator\Request\Exception\InvalidMethod::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            [],
            [],
            [],
            [],
            'PATCH',
        );
        (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();
    }

    public function testInvalidMultipartGet() : void
    {
        $this->expectException(\Graphpinator\Request\Exception\InvalidMultipartRequest::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            ['map' => '[]', 'operations' => '{"query":"query {}", "variables":{"var1":"varValue"}, "operationName":"opName"}'],
            [],
            [],
            ['Content-Type' => 'multipart/form-data; boundary=--------boundary'],
            'GET',
        );
        (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();
    }

    public function testInvalidStrict() : void
    {
        $this->expectException(\Graphpinator\Request\Exception\UnknownKey::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            [],
            [],
            [],
            ['Content-Type' => 'application/json'],
            'GET',
            null,
            null,
            static function() {
                return '{"query":"query {}", "variables": {"var1":"varValue"}, "bla": "opName"}';
            },
        );
        (new \Graphpinator\Nette\NetteRequestFactory($httpRequest))->create();
    }
}

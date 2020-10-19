<?php

declare(strict_types = 1);

namespace Graphpinator\Nette\Tests\Unit;

final class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function testJsonBody() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            null,
            null,
            null,
            ['Content-Type' => 'application/json'],
            'GET',
            null,
            null,
            static function() {
                return '{"query":"query {}", "variables": {"var1":"varValue"}, "operationName": "opName"}';
            },
        );
        $request = \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testGraphqlBody() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            null,
            null,
            null,
            ['Content-Type' => 'application/graphql'],
            'GET',
            null,
            null,
            static function() {
                return 'query {}';
            },
        );
        $request = \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);

        self::assertSame('query {}', $request->getQuery());
        self::assertSame([], (array) $request->getVariables());
        self::assertNull($request->getOperationName());
    }

    public function testQueryParams() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript('/?query=query%20{}&variables={"var1":"varValue"}&operationName=opName'),
        );
        $request = \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testMultipart() : void
    {
        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            ['map' => '[]', 'operations' => '{"query":"query {}", "variables":{"var1":"varValue"}, "operationName":"opName"}'],
            null,
            null,
            ['Content-Type' => 'multipart/form-data; boundary=--------boundary'],
            'POST',
        );
        $request = \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);

        self::assertSame('query {}', $request->getQuery());
        self::assertSame(['var1' => 'varValue'], (array) $request->getVariables());
        self::assertSame('opName', $request->getOperationName());
    }

    public function testInvalidMethod() : void
    {
        $this->expectException(\Graphpinator\Exception\Request\InvalidMethod::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            null,
            null,
            null,
            null,
            'PATCH',
        );
        \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);
    }

    public function testInvalidMultipartGet() : void
    {
        $this->expectException(\Graphpinator\Exception\Request\InvalidMultipartRequest::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            ['map' => '[]', 'operations' => '{"query":"query {}", "variables":{"var1":"varValue"}, "operationName":"opName"}'],
            null,
            null,
            ['Content-Type' => 'multipart/form-data; boundary=--------boundary'],
            'GET',
        );
        \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);
    }

    public function testInvalidStrict() : void
    {
        $this->expectException(\Graphpinator\Exception\Request\UnknownKey::class);

        $httpRequest = new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            null,
            null,
            null,
            ['Content-Type' => 'application/json'],
            'GET',
            null,
            null,
            static function() {
                return '{"query":"query {}", "variables": {"var1":"varValue"}, "bla": "opName"}';
            },
        );
        \Graphpinator\Nette\Request::fromNetteHttpRequest($httpRequest);
    }
}

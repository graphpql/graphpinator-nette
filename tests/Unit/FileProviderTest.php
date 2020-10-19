<?php

declare(strict_types = 1);

namespace Graphpinator\Nette\Tests\Unit;

final class FileProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @backupGlobals
     */
    public function testSimple() : void
    {
        $_FILES['0'] = ['type' => 'text/plain'];
        $fileProvider = new \Graphpinator\Nette\FileProvider(new \Nette\Http\Request(
            new \Nette\Http\UrlScript(),
            ['map' => '{"0": ["variables.file"]}'],
            [
                '0' => new \Nette\Http\FileUpload([
                    'name' => 'file.txt',
                    'size' => 123,
                    'tmp_name' => '/tmp/file.txt',
                    'error' => 0,
                ]),
            ],
        ));

        self::assertCount(1, $fileProvider->getMap());
        self::assertArrayHasKey('0', $fileProvider->getMap());
        self::assertSame(['variables.file'], $fileProvider->getMap()['0']);

        $file = $fileProvider->getFile('0');
        self::assertSame('file.txt', $file->getClientFilename());
        self::assertSame(123, $file->getSize());
        self::assertSame(0, $file->getError());
        self::assertSame('text/plain', $file->getClientMediaType());
    }
}

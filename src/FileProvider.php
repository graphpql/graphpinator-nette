<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class FileProvider implements \Graphpinator\Module\Upload\FileProvider
{
    use \Nette\SmartObject;

    private \Nette\Http\Request $request;

    public function __construct(\Nette\Http\Request $request)
    {
        $this->request = $request;
    }

    public function getMap() : \Infinityloop\Utils\Json\MapJson
    {
        return \Infinityloop\Utils\Json\MapJson::fromString($this->request->getPost('map'));
    }

    public function getFile(string $key) : \Psr\Http\Message\UploadedFileInterface
    {
        $file = $this->request->getFile($key);

        return new \GuzzleHttp\Psr7\UploadedFile(
            $file->getTemporaryFile(),
            $file->getSize(),
            $file->getError(),
            $file->getUntrustedName(),
            $_FILES[$key]['type'],
        );
    }
}

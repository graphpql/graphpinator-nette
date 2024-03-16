<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class FileProvider implements \Graphpinator\Upload\FileProvider
{
    use \Nette\SmartObject;

    public function __construct(
        private \Nette\Http\Request $request,
    )
    {
    }

    public function getMap() : ?\Infinityloop\Utils\Json
    {
        $map = $this->request->getPost('map');

        return \is_string($map)
            ? \Infinityloop\Utils\Json::fromString($map)
            : null;
    }

    public function getFile(string $key) : \Psr\Http\Message\UploadedFileInterface
    {
        $file = $this->request->getFile($key);

        if (!$file instanceof \Nette\Http\FileUpload) {
            throw new \LogicException();
        }

        return new \GuzzleHttp\Psr7\UploadedFile(
            $file->getTemporaryFile(),
            $file->getSize(),
            $file->getError(),
            $file->getUntrustedName(),
            $_FILES[$key]['type'],
        );
    }
}

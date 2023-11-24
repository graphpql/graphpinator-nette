<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class TextFileResponse implements \Nette\Application\Response
{
    public function __construct(
        private string $content,
    )
    {
    }

    public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse) : void
    {
        $httpResponse->setContentType('application/graphql');
        $httpResponse->setHeader('Content-Disposition', 'attachment;filename="schema.graphql"');
        $httpResponse->setHeader('Content-Length', (string) \strlen($this->content));

        echo $this->content;
    }
}

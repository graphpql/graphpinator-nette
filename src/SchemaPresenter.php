<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class SchemaPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
        private \Graphpinator\Typesystem\Schema $schema,
    )
    {
        parent::__construct();
    }

    public function actionHtml() : void
    {
        $printer = new \Graphpinator\Printer\Printer(new \Graphpinator\Printer\HtmlVisitor(), new \Graphpinator\Printer\TypeKindSorter());

        $this->template->setFile(__DIR__ . '/schema.latte');
        $this->template->schema = $printer->printSchema($this->schema);
    }

    public function actionFile() : void
    {
        $printer = new \Graphpinator\Printer\Printer(sorter: new \Graphpinator\Printer\TypeKindSorter());
        $response = new \Graphpinator\Nette\TextFileResponse($printer->printSchema($this->schema));

        $this->sendResponse($response);
    }
}

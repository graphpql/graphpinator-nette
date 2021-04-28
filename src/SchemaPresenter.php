<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class SchemaPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
        private \Graphpinator\Type\Schema $schema,
    )
    {
        parent::__construct();
    }

    public function actionDefault() : void
    {
        $printer = new \Graphpinator\Printer\Printer(new \Graphpinator\Printer\HtmlVisitor(), new \Graphpinator\Printer\TypeKindSorter());

        $this->template->setFile(__DIR__ . '/schema.latte');
        $this->template->schema = $printer->printSchema($this->schema);
    }

    public function actionText() : void
    {
        $printer = new \Graphpinator\Printer\Printer(new \Graphpinator\Printer\TextVisitor(), new \Graphpinator\Printer\TypeKindSorter());
        $response = new \Graphpinator\Nette\TextFileResponse($printer->printSchema($this->schema));

        $this->sendResponse($response);
    }
}

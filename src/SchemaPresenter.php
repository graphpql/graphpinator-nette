<?php

declare(strict_types = 1);

namespace App\Module\Api\Presenter;

final class SchemaPresenter extends \Nette\Application\UI\Presenter
{
    private \Graphpinator\Type\Schema $schema;

    public function __construct(\Graphpinator\Type\Schema $schema)
    {
        parent::__construct();

        $this->schema = $schema;
    }

    public function actionDefault() : void
    {
        $this->template->setFile(__DIR__ . '/schema.latte');
        $this->template->schema = $this->schema->printSchema(new \Graphpinator\Utils\Sort\TypeKindSorter());
    }
}

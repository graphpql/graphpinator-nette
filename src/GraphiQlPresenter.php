<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class GraphiQlPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
        private string $apiLocation,
    )
    {
        parent::__construct();
    }

    public function actionDefault() : void
    {
        $this->template->setFile(__DIR__ . '/graphiql.latte');
        $this->template->apiLocation = $this->apiLocation;
    }
}

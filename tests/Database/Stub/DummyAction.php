<?php

namespace MiniCore\Tests\Database\Stub;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;

/**
 * Class DummyAction
 *
 * A dummy implementation of AbstractAction for testing purposes.
 */
class DummyAction extends AbstractAction
{
    public function execute(string $repositoryName, ?DataAction $data): mixed
    {
        return $data;
    }

    public function validate(DataAction $data): bool
    {
        return !empty($data->getColumns());
    }
}

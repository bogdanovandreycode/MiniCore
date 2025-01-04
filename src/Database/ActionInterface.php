<?php

namespace Vendor\Undermarket\Core\Database;

interface ActionInterface
{
    public function getName(): string;
    public function execute(DataAction $data): mixed;
    public function validate(DataAction $data): bool;
}

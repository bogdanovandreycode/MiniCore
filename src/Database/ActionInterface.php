<?php

namespace MiniCore\Database;

interface ActionInterface
{
    public function getName(): string;
    public function execute(DataAction $data): mixed;
    public function validate(DataAction $data): bool;
}

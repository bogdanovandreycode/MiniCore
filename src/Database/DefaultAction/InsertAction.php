<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

class InsertAction implements ActionInterface
{
    public function __construct(
        public string $tableName,
    ) {}

    public function getName(): string
    {
        return 'insert';
    }

    public function execute(DataAction $data): mixed
    {
        $columns = implode(', ', $data->getKeyColumns());
        $placeholders = implode(', ', array_map(fn($key) => ":$key", $data->getKeyColumns()));
        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        $result = DataBase::execute($sql, $data->getColumns());
        return $result;
    }

    public function validate(DataAction $data): bool
    {
        if (empty($data->getColumns())) {
            return false;
        }

        return true;
    }
}

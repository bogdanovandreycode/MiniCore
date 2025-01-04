<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

class SelectAction implements ActionInterface
{
    public function __construct(
        public string $tableName,
    ) {}

    public function getName(): string
    {
        return 'select';
    }

    public function execute(DataAction $data): mixed
    {
        $sql = "SELECT ";
        $sql .= implode(', ', $data->getKeyColumns());
        $sql .= " FROM {$this->tableName}";

        foreach ($data->getProperties() as $key => $value) {
            $sql .= $key . ' ' . $value . ' ';
        }

        $result = DataBase::query($sql, $data->getColumns());
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

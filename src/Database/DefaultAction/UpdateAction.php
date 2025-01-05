<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

class UpdateAction implements ActionInterface
{
    public function __construct(
        public string $tableName,
    ) {}

    public function getName(): string
    {
        return 'update';
    }

    public function execute(DataAction $data): mixed
    {
        // Формирование части SET
        $updateColumns = $data->getColumns();
        $setClauses = [];

        foreach ($updateColumns as $column) {
            $setClauses[] = "$column = :set_$column";
        }

        $setClause = implode(', ', $setClauses);

        // Формирование условий WHERE
        $whereConditions = implode(' ', $data->getProperty('WHERE'));

        // Генерация SQL
        $sql = "UPDATE {$this->tableName} SET $setClause";

        if (!empty($whereConditions)) {
            $sql .= " WHERE $whereConditions";
        }

        // Параметры для prepared statements
        $parameters = [];

        foreach ($updateColumns as $column) {
            $parameters["set_$column"] = $data->getParameters()["set_$column"] ?? null;
        }

        $parameters = array_merge($parameters, $data->getParameters());

        // Выполнение SQL
        return DataBase::execute($sql, $parameters);
    }

    public function validate(DataAction $data): bool
    {
        // Проверка наличия колонок для обновления
        if (empty($data->getColumns())) {
            return false;
        }

        return true;
    }
}

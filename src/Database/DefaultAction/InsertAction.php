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
        // Получаем колонки для INSERT
        $columns = $data->getColumns();
        if (empty($columns)) {
            throw new \RuntimeException("No columns provided for insert operation.");
        }

        // Генерируем список колонок и плейсхолдеров
        $columnsList = implode(', ', $columns);
        $placeholders = implode(', ', array_map(fn($column) => ":$column", $columns));

        // Формируем SQL
        $sql = "INSERT INTO {$this->tableName} ($columnsList) VALUES ($placeholders)";

        // Выполняем запрос
        return DataBase::execute($sql, $data->getParameters());
    }

    public function validate(DataAction $data): bool
    {
        // Проверяем наличие колонок для вставки
        return !empty($data->getColumns());
    }
}

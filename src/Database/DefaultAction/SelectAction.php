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
        // Формируем SELECT
        $selectColumns = $data->getColumns();
        $sql = "SELECT " . (!empty($selectColumns) ? implode(', ', $selectColumns) : '*');

        // Указываем таблицу
        $sql .= " FROM {$this->tableName}";

        // Формируем свойства в порядке добавления
        foreach ($data->getProperties() as $property) {
            $sql .= " {$property['type']} {$property['condition']}";
        }

        // Параметры
        $parameters = $data->getParameters();

        // Выполняем запрос
        return DataBase::query($sql, $parameters);
    }

    public function validate(DataAction $data): bool
    {
        // Проверяем, есть ли хотя бы одна колонка для выборки
        if (empty($data->getColumns())) {
            return false;
        }

        return true;
    }
}

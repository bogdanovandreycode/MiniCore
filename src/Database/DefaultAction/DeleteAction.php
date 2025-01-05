<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\ActionInterface;
use MiniCore\Database\DataAction;
use MiniCore\Database\DataBase;

class DeleteAction implements ActionInterface
{
    public function __construct(
        public string $tableName,
    ) {}

    public function getName(): string
    {
        return 'delete';
    }

    public function execute(DataAction $data): mixed
    {
        // Формируем SQL
        $sql = "DELETE FROM {$this->tableName}";

        // Добавляем условия (WHERE и другие)
        foreach ($data->getProperties() as $property) {
            $sql .= " {$property['type']} {$property['condition']}";
        }

        // Параметры для prepared statements
        $parameters = $data->getParameters();

        // Выполняем запрос
        return DataBase::execute($sql, $parameters);
    }

    public function validate(DataAction $data): bool
    {
        // Проверка: наличие хотя бы одного условия для удаления
        $hasConditions = !empty($data->getProperties());
        return $hasConditions;
    }
}

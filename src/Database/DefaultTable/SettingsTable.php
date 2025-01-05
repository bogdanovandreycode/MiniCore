<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;
use MiniCore\Database\DataAction;

class SettingsTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'settings',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'key_name' => 'VARCHAR(255) NOT NULL UNIQUE',
                'value' => 'TEXT NOT NULL',
            ]
        );
    }

    /**
     * Получить значение мета-данных
     *
     * @param string $key Ключ мета-данных
     * @param bool $isSingle Вернуть только одно значение или массив
     * @return mixed Массив или значение
     */
    public function getOption(string $key)
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('key_name');
        $dataAction->addColumn('value');
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);
        $result = $this->actions['select']->execute($dataAction);
        return $result[0]['value'];
    }

    /**
     * Обновить мета-данные
     *
     * @param int $userId ID пользователя
     * @param string $key Ключ мета-данных
     * @param string $value Значение мета-данных
     * @return bool Результат выполнения
     */
    public function updateOption(string $key, string $value): bool
    {
        $existingMeta = $this->getOption($key);

        if ($existingMeta === null) {
            return $this->addOption($key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('value');
        $dataAction->addParameters(['value' => $value]);

        $dataAction->addProperty('WHERE', 'key_name = :key_name', [
            'key_name' => $key,
        ]);

        return $this->actions['update']->execute($dataAction);
    }

    /**
     * Добавить мета-данные
     *
     * @param int $userId ID пользователя
     * @param string $key Ключ мета-данных
     * @param string $value Значение мета-данных
     * @return bool Результат выполнения
     */
    public function addOption(string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('user_id');
        $dataAction->addColumn('key_name');
        $dataAction->addColumn('value');

        $dataAction->addParameters([
            'key_name' => $key,
            'value' => $value,
        ]);

        return $this->actions['insert']->execute($dataAction);
    }
}

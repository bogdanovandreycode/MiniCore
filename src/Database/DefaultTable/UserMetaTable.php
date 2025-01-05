<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;
use MiniCore\Database\DataAction;

class UserMetaTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'usermeta',
            [
                'meta_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'user_id' => 'INT NOT NULL',
                'meta_key' => 'VARCHAR(255) NOT NULL',
                'meta_value' => 'TEXT',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
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
    public function getMeta(string $key, bool $isSingle = true)
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');
        $dataAction->addProperty('WHERE', 'meta_key = :meta_key', ['meta_key' => $key]);

        $result = $this->actions['select']->execute($dataAction);

        if ($result) {
            return $isSingle
                ? ($result[0]['meta_value'] ?? null)
                : array_column($result, 'meta_value', 'meta_key');
        }

        return $isSingle ? null : [];
    }

    /**
     * Обновить мета-данные
     *
     * @param int $userId ID пользователя
     * @param string $key Ключ мета-данных
     * @param string $value Значение мета-данных
     * @return bool Результат выполнения
     */
    public function updateMeta(int $userId, string $key, string $value): bool
    {
        $existingMeta = $this->getMeta($key, true);

        if ($existingMeta === null) {
            return $this->addMeta($userId, $key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('meta_value');
        $dataAction->addParameters(['meta_value' => $value]);
        $dataAction->addProperty('WHERE', 'meta_key = :meta_key AND user_id = :user_id', [
            'meta_key' => $key,
            'user_id' => $userId,
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
    public function addMeta(int $userId, string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('user_id');
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');

        $dataAction->addParameters([
            'user_id' => $userId,
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        return $this->actions['insert']->execute($dataAction);
    }
}

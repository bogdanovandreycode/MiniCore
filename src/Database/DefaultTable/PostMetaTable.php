<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;
use MiniCore\Database\DataAction;

class PostMetaTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'postmeta',
            [
                'meta_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'post_id' => 'INT NOT NULL',
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
     * @param int $postId ID пользователя
     * @param string $key Ключ мета-данных
     * @param string $value Значение мета-данных
     * @return bool Результат выполнения
     */
    public function updateMeta(int $postId, string $key, string $value): bool
    {
        $existingMeta = $this->getMeta($key, true);

        if ($existingMeta === null) {
            return $this->addMeta($postId, $key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('meta_value');
        $dataAction->addParameters(['meta_value' => $value]);

        $dataAction->addProperty('WHERE', 'meta_key = :meta_key AND post_id = :post_id', [
            'meta_key' => $key,
            'post_id' => $postId,
        ]);

        return $this->actions['update']->execute($dataAction);
    }

    /**
     * Добавить мета-данные
     *
     * @param int $postId ID пользователя
     * @param string $key Ключ мета-данных
     * @param string $value Значение мета-данных
     * @return bool Результат выполнения
     */
    public function addMeta(int $postId, string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('post_id');
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');

        $dataAction->addParameters([
            'post_id' => $postId,
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        return $this->actions['insert']->execute($dataAction);
    }
}

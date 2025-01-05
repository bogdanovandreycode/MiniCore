<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;
use MiniCore\Database\DataAction;

class UserRolesTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'user_roles',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'userId' => 'INT NOT NULL',
                'roleId' => 'INT NOT NULL',
            ]
        );
    }

    /**
     * Получить роли пользователя по его ID.
     *
     * @param int $userId ID пользователя.
     * @return array Список ролей.
     */
    public function getRolesByUserId(int $userId): array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('roleId');
        $dataAction->addProperty('WHERE', 'userId = :userId', ['userId' => $userId]);
        return $this->actions['select']->execute($dataAction);
    }

    /**
     * Получить пользователей, у которых есть определённая роль.
     *
     * @param int $roleId ID роли.
     * @return array Список пользователей.
     */
    public function getUsersByRoleId(int $roleId): array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('userId');
        $dataAction->addProperty('WHERE', 'roleId = :roleId', ['roleId' => $roleId]);
        return $this->actions['select']->execute($dataAction);
    }

    /**
     * Добавить роль пользователю.
     *
     * @param int $userId ID пользователя.
     * @param int $roleId ID роли.
     * @return bool Результат выполнения.
     */
    public function addRoleToUser(int $userId, int $roleId): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('userId');
        $dataAction->addColumn('roleId');

        $dataAction->addParameters([
            'userId' => $userId,
            'roleId' => $roleId,
        ]);

        return $this->actions['insert']->execute($dataAction);
    }

    /**
     * Удалить роль у пользователя.
     *
     * @param int $userId ID пользователя.
     * @param int $roleId ID роли.
     * @return bool Результат выполнения.
     */
    public function removeRoleFromUser(int $userId, int $roleId): bool
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'userId = :userId AND roleId = :roleId', [
            'userId' => $userId,
            'roleId' => $roleId,
        ]);

        return $this->actions['delete']->execute($dataAction);
    }

    /**
     * Проверить, есть ли у пользователя определённая роль.
     *
     * @param int $userId ID пользователя.
     * @param int $roleId ID роли.
     * @return bool True, если роль есть, иначе False.
     */
    public function hasRole(int $userId, int $roleId): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('id');

        $dataAction->addProperty('WHERE', 'userId = :userId AND roleId = :roleId', [
            'userId' => $userId,
            'roleId' => $roleId,
        ]);

        $dataAction->addProperty('LIMIT', '1');
        $result = $this->actions['select']->execute($dataAction);
        return !empty($result);
    }
}

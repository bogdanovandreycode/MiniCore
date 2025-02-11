<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class RolesTable
 *
 * This class defines the structure and functionality for the `roles` table in the database.
 * It manages user roles and their associated permissions.
 *
 * The `roles` table structure includes:
 * - `id`: Unique identifier for each role.
 * - `name`: The name of the role (e.g., admin, editor, user).
 * - `permissions`: A JSON-encoded array of permissions assigned to the role.
 *
 * @example
 * // Example of usage:
 * $rolesTable = new RolesTable();
 * 
 * // Use select action
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('role');
 * $dataAction->addProperty('WHERE', 'role = :role', ['role' => 'editor']);
 * $postsTable->actions['select']->execute($dataAction);
 *
 * // Get permissions for a specific role by its name
 * $permissions = $rolesTable->getPermissionsByRole('editor');
 */
class RolesTable extends AbstractTable
{
    /**
     * RolesTable constructor.
     *
     * Initializes the `roles` table with its structure and columns.
     */
    public function __construct()
    {
        parent::__construct(
            'roles',
            'mysql',
            [
                'id'          => 'INT AUTO_INCREMENT PRIMARY KEY',  // Unique role identifier
                'name'        => 'VARCHAR(255) NOT NULL UNIQUE',   // Role name (must be unique)
                'permissions' => 'JSON NOT NULL',                  // JSON array of permissions
            ]
        );
    }

    /**
     * Get permissions for a specific role by its name.
     *
     * @param string $roleName The name of the role.
     * @return array|null Array of permissions or null if role not found.
     *
     * @example
     * $permissions = $rolesTable->getPermissionsByRole('editor');
     */
    public function getPermissionsByRole(string $roleName): ?array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('permissions');
        $dataAction->addProperty('WHERE', 'name = :name', ['name' => $roleName]);
        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);
        return $result ? json_decode($result[0]['permissions'], true) : null;
    }
}

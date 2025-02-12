<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class RolesTable
 *
 * Manages user roles and their associated permissions in the `roles` table.
 * Provides methods to retrieve and manage role-based permissions.
 *
 * The `roles` table structure includes:
 * - `id`: Unique identifier for each role.
 * - `name`: The name of the role (e.g., admin, editor, user).
 * - `permissions`: A JSON-encoded array of permissions assigned to the role.
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $rolesTable = new RolesTable();
 *
 * // Retrieve permissions for a specific role
 * $permissions = $rolesTable->getPermissionsByRole('editor');
 * print_r($permissions);
 *
 * // Retrieve all roles
 * $roles = $rolesTable->getAllRoles();
 * print_r($roles);
 */
class RolesTable extends AbstractTable
{
    /**
     * RolesTable constructor.
     *
     * Initializes the `roles` table with its structure and columns.
     *
     * @example
     * $rolesTable = new RolesTable();
     */
    public function __construct()
    {
        parent::__construct(
            'roles',
            'mysql',
            [
                'id'          => 'INT AUTO_INCREMENT PRIMARY KEY', // Unique role identifier
                'name'        => 'VARCHAR(255) NOT NULL UNIQUE',  // Role name (must be unique)
                'permissions' => 'JSON NOT NULL',                 // JSON-encoded array of permissions
            ]
        );
    }

    /**
     * Retrieve permissions for a specific role by its name.
     *
     * @param string $roleName The name of the role.
     * @return array|null The permissions as an associative array, or null if the role is not found.
     *
     * @example
     * // Retrieve permissions for the "editor" role
     * $permissions = $rolesTable->getPermissionsByRole('editor');
     * print_r($permissions);
     */
    public function getPermissionsByRole(string $roleName): ?array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('permissions');
        $dataAction->addProperty('WHERE', 'name = :name', ['name' => $roleName]);

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

        return $result ? json_decode($result[0]['permissions'], true) : null;
    }

    /**
     * Retrieve all roles from the database.
     *
     * @return array List of roles, each containing `id`, `name`, and `permissions`.
     *
     * @example
     * // Retrieve all roles
     * $roles = $rolesTable->getAllRoles();
     * print_r($roles);
     */
    public function getAllRoles(): array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('id');
        $dataAction->addColumn('name');
        $dataAction->addColumn('permissions');

        return $this->actions['select']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Add a new role to the database.
     *
     * @param string $roleName The name of the new role.
     * @param array $permissions The permissions associated with the role.
     * @return bool True if the role was successfully added, false otherwise.
     *
     * @example
     * // Add a new "moderator" role with specific permissions
     * $rolesTable->addRole('moderator', ['edit_posts', 'delete_posts']);
     */
    public function addRole(string $roleName, array $permissions): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('name');
        $dataAction->addColumn('permissions');

        $dataAction->addParameters([
            'name'        => $roleName,
            'permissions' => json_encode($permissions),
        ]);

        return $this->actions['insert']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Delete a role by its name.
     *
     * @param string $roleName The name of the role to delete.
     * @return bool True if the deletion was successful, false otherwise.
     *
     * @example
     * // Delete the "moderator" role
     * $rolesTable->deleteRole('moderator');
     */
    public function deleteRole(string $roleName): bool
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'name = :name', ['name' => $roleName]);

        return $this->actions['delete']->execute($this->repositoryName, $dataAction);
    }
}

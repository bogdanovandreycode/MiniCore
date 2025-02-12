<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class UsersTable
 *
 * Represents the `users` table in the database.
 * This table stores user authentication and profile data, including usernames, emails, password hashes, and role associations.
 *
 * **Table Structure:**
 * - `id`: Unique identifier for each user (auto-increment, primary key).
 * - `username`: User's login name (must be unique).
 * - `email`: User's email address (unique, used for login and notifications).
 * - `password_hash`: Hashed password for secure authentication.
 * - `role_id`: Role ID linking the user to a specific role (for access control).
 * - `created_at`: Timestamp of when the user was created (default: current timestamp).
 * - `updated_at`: Timestamp of the last update to the user's record (automatically updated).
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $usersTable = new UsersTable();
 *
 * // Retrieve a user by email
 * $user = $usersTable->getUserByEmail('john@example.com');
 * print_r($user);
 *
 * // Update a user's role
 * $usersTable->updateUserRole(1, 3);
 *
 * // Retrieve all users
 * $users = $usersTable->getAllUsers();
 * print_r($users);
 */
class UsersTable extends AbstractTable
{
    /**
     * UsersTable constructor.
     *
     * Initializes the `users` table with its columns and structure.
     *
     * @example
     * $usersTable = new UsersTable();
     */
    public function __construct()
    {
        parent::__construct(
            'users',
            'mysql',
            [
                'id'            => 'INT AUTO_INCREMENT PRIMARY KEY',  // Unique user ID
                'username'      => 'VARCHAR(255) NOT NULL',           // Username
                'email'         => 'VARCHAR(255) UNIQUE NOT NULL',    // Email address
                'password_hash' => 'VARCHAR(255) NOT NULL',           // Hashed password
                'role_id'       => 'INT NOT NULL',                    // Role ID
                'created_at'    => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',               // Created timestamp
                'updated_at'    => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', // Updated timestamp
            ]
        );
    }

    /**
     * Retrieve a user by their email address.
     *
     * @param string $email The user's email address.
     * @return array|null User data as an associative array or null if not found.
     *
     * @example
     * // Retrieve user by email
     * $user = $usersTable->getUserByEmail('jane@example.com');
     * print_r($user);
     */
    public function getUserByEmail(string $email): ?array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('*');
        $dataAction->addProperty('WHERE', 'email = :email', ['email' => $email]);
        $dataAction->addProperty('LIMIT', '1');

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

        return $result[0] ?? null;
    }

    /**
     * Retrieve all users from the database.
     *
     * @return array List of all users.
     *
     * @example
     * // Retrieve all users
     * $users = $usersTable->getAllUsers();
     * print_r($users);
     */
    public function getAllUsers(): array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('*');

        return $this->actions['select']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Update the user's role.
     *
     * @param int $userId The ID of the user.
     * @param int $roleId The new role ID.
     * @return bool True if the update was successful, false otherwise.
     *
     * @example
     * // Update user role
     * $usersTable->updateUserRole(1, 3);
     */
    public function updateUserRole(int $userId, int $roleId): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('role_id');
        $dataAction->addParameters(['role_id' => $roleId]);
        $dataAction->addProperty('WHERE', 'id = :id', ['id' => $userId]);

        return $this->actions['update']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Delete a user by their ID.
     *
     * @param int $userId The ID of the user to delete.
     * @return bool True if the deletion was successful, false otherwise.
     *
     * @example
     * // Delete user
     * $usersTable->deleteUser(5);
     */
    public function deleteUser(int $userId): bool
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'id = :id', ['id' => $userId]);

        return $this->actions['delete']->execute($this->repositoryName, $dataAction);
    }
}

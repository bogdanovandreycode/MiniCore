<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class UserMetaTable
 *
 * Manages user-specific metadata stored in the `usermeta` table.
 * Provides methods to retrieve, update, and insert custom metadata for users.
 *
 * Table structure:
 * - `meta_id`: Unique identifier for each metadata entry (auto-increment).
 * - `user_id`: The ID of the user associated with the metadata.
 * - `meta_key`: The key representing the metadata.
 * - `meta_value`: The value of the metadata.
 * - `created_at`: Timestamp when the metadata was created.
 * - `updated_at`: Timestamp when the metadata was last updated.
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $userMetaTable = new UserMetaTable();
 *
 * // Add new user metadata
 * $userMetaTable->addMeta(1, 'theme', 'dark');
 *
 * // Update existing user metadata
 * $userMetaTable->updateMeta(1, 'theme', 'light');
 *
 * // Retrieve user metadata
 * $theme = $userMetaTable->getMeta(1, 'theme');
 * echo $theme;
 */
class UserMetaTable extends AbstractTable
{
    /**
     * UserMetaTable constructor.
     *
     * Initializes the `usermeta` table with its structure and columns.
     *
     * @example
     * $userMetaTable = new UserMetaTable();
     */
    public function __construct()
    {
        parent::__construct(
            'usermeta',
            'mysql',
            [
                'meta_id'    => 'INT AUTO_INCREMENT PRIMARY KEY', // Unique meta ID
                'user_id'    => 'INT NOT NULL',                   // Associated user ID
                'meta_key'   => 'VARCHAR(255) NOT NULL',          // Metadata key
                'meta_value' => 'TEXT',                           // Metadata value
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',                       // Creation time
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', // Update time
            ]
        );
    }

    /**
     * Retrieve user metadata by user ID and key.
     *
     * @param int $userId The ID of the user.
     * @param string $key The metadata key to search for.
     * @param bool $isSingle Whether to return a single value (default: true).
     * @return string|array|null The metadata value if `$isSingle` is `true`, or an array of key-value pairs otherwise.
     *
     * @example
     * // Get a single metadata value
     * $theme = $userMetaTable->getMeta(1, 'theme');
     * echo $theme;
     *
     * // Get all metadata values for the key
     * $allThemes = $userMetaTable->getMeta(1, 'theme', false);
     * print_r($allThemes);
     */
    public function getMeta(int $userId, string $key, bool $isSingle = true): string|array|null
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('meta_value');
        $dataAction->addProperty('WHERE', 'user_id = :user_id AND meta_key = :meta_key', [
            'user_id'  => $userId,
            'meta_key' => $key,
        ]);

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

        if ($result) {
            return $isSingle
                ? ($result[0]['meta_value'] ?? null)
                : array_column($result, 'meta_value', 'meta_key');
        }

        return $isSingle ? null : [];
    }

    /**
     * Update an existing user metadata or insert it if it does not exist.
     *
     * @param int $userId The ID of the user.
     * @param string $key The metadata key.
     * @param string $value The new value for the metadata.
     * @return bool True if the update or insert was successful, false otherwise.
     *
     * @example
     * // Update or insert user metadata
     * $userMetaTable->updateMeta(1, 'theme', 'light');
     */
    public function updateMeta(int $userId, string $key, string $value): bool
    {
        if ($this->getMeta($userId, $key, true) === null) {
            return $this->addMeta($userId, $key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('meta_value');
        $dataAction->addParameters(['meta_value' => $value]);
        $dataAction->addProperty('WHERE', 'user_id = :user_id AND meta_key = :meta_key', [
            'user_id'  => $userId,
            'meta_key' => $key,
        ]);

        return $this->actions['update']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Add new metadata for a user.
     *
     * @param int $userId The ID of the user.
     * @param string $key The metadata key.
     * @param string $value The metadata value.
     * @return bool True if the insert was successful, false otherwise.
     *
     * @example
     * // Add new metadata for a user
     * $userMetaTable->addMeta(1, 'theme', 'dark');
     */
    public function addMeta(int $userId, string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('user_id');
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');

        $dataAction->addParameters([
            'user_id'    => $userId,
            'meta_key'   => $key,
            'meta_value' => $value,
        ]);

        return $this->actions['insert']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Delete metadata for a user by key.
     *
     * @param int $userId The ID of the user.
     * @param string $key The metadata key to delete.
     * @return bool True if the deletion was successful, false otherwise.
     *
     * @example
     * // Delete metadata for a user
     * $userMetaTable->deleteMeta(1, 'theme');
     */
    public function deleteMeta(int $userId, string $key): bool
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'user_id = :user_id AND meta_key = :meta_key', [
            'user_id'  => $userId,
            'meta_key' => $key,
        ]);

        return $this->actions['delete']->execute($this->repositoryName, $dataAction);
    }
}

<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class SettingsTable
 *
 * Manages application-wide settings stored in the `settings` table.
 * Provides methods to retrieve, update, and add configuration settings.
 *
 * Table structure:
 * - `id`: Unique identifier for each setting (auto-increment).
 * - `key_name`: The unique key representing the setting.
 * - `value`: The value associated with the key.
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $settingsTable = new SettingsTable();
 *
 * // Add a new setting
 * $settingsTable->addOption('site_name', 'My Website');
 *
 * // Update an existing setting
 * $settingsTable->updateOption('site_name', 'Updated Website');
 *
 * // Get a setting value
 * $siteName = $settingsTable->getOption('site_name');
 * echo $siteName;
 */
class SettingsTable extends AbstractTable
{
    /**
     * SettingsTable constructor.
     *
     * Initializes the `settings` table with its structure and columns.
     *
     * @example
     * $settingsTable = new SettingsTable();
     */
    public function __construct()
    {
        parent::__construct(
            'settings',
            'mysql',
            [
                'id'        => 'INT AUTO_INCREMENT PRIMARY KEY', // Unique setting ID
                'key_name'  => 'VARCHAR(255) NOT NULL UNIQUE',   // Unique key for the setting
                'value'     => 'TEXT NOT NULL',                 // Value of the setting
            ]
        );
    }

    /**
     * Retrieve a setting value by its key.
     *
     * @param string $key The key of the setting to retrieve.
     * @return string|null The setting value or null if not found.
     *
     * @example
     * // Get a setting value
     * $siteName = $settingsTable->getOption('site_name');
     * echo $siteName;
     */
    public function getOption(string $key): ?string
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('value');
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

        return $result[0]['value'] ?? null;
    }

    /**
     * Update an existing setting or insert it if it does not exist.
     *
     * @param string $key The key of the setting to update.
     * @param string $value The new value for the setting.
     * @return bool True if the update or insert was successful, false otherwise.
     *
     * @example
     * // Update an existing setting
     * $settingsTable->updateOption('site_name', 'New Website Name');
     */
    public function updateOption(string $key, string $value): bool
    {
        if ($this->getOption($key) === null) {
            return $this->addOption($key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('value');
        $dataAction->addParameters(['value' => $value]);
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);

        return $this->actions['update']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Add a new setting to the database.
     *
     * @param string $key The key of the new setting.
     * @param string $value The value of the new setting.
     * @return bool True if the insert was successful, false otherwise.
     *
     * @example
     * // Add a new setting
     * $settingsTable->addOption('theme_color', '#FFFFFF');
     */
    public function addOption(string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('key_name');
        $dataAction->addColumn('value');

        $dataAction->addParameters([
            'key_name' => $key,
            'value'    => $value,
        ]);

        return $this->actions['insert']->execute($this->repositoryName, $dataAction);
    }

    /**
     * Delete a setting by its key.
     *
     * @param string $key The key of the setting to delete.
     * @return bool True if the deletion was successful, false otherwise.
     *
     * @example
     * // Delete a setting
     * $settingsTable->deleteOption('site_name');
     */
    public function deleteOption(string $key): bool
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);

        return $this->actions['delete']->execute($this->repositoryName, $dataAction);
    }
}

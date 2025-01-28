<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;
use MiniCore\Database\Action\DataAction;

/**
 * Class SettingsTable
 *
 * This class manages the `settings` table in the database, providing methods
 * to interact with application-wide settings or configurations.
 *
 * Table structure:
 * - `id`: Unique identifier for each setting (auto-increment).
 * - `key_name`: The unique key representing the setting.
 * - `value`: The value associated with the key.
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
 */
class SettingsTable extends Table
{
    /**
     * SettingsTable constructor.
     *
     * Initializes the `settings` table with its structure and columns.
     */
    public function __construct()
    {
        parent::__construct(
            'settings',
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
     * @return mixed|null The setting value or null if not found.
     *
     * @example
     * $siteName = $settingsTable->getOption('site_name');
     */
    public function getOption(string $key)
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('key_name');
        $dataAction->addColumn('value');
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);

        $result = $this->actions['select']->execute($dataAction);
        return $result[0]['value'] ?? null;
    }

    /**
     * Update an existing setting or insert it if it doesn't exist.
     *
     * @param string $key The key of the setting to update.
     * @param string $value The new value for the setting.
     * @return bool True if the update or insert was successful, false otherwise.
     *
     * @example
     * $settingsTable->updateOption('site_name', 'New Website Name');
     */
    public function updateOption(string $key, string $value): bool
    {
        $existingOption = $this->getOption($key);

        if ($existingOption === null) {
            return $this->addOption($key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('value');
        $dataAction->addParameters(['value' => $value]);
        $dataAction->addProperty('WHERE', 'key_name = :key_name', ['key_name' => $key]);

        return $this->actions['update']->execute($dataAction);
    }

    /**
     * Add a new setting to the database.
     *
     * @param string $key The key of the new setting.
     * @param string $value The value of the new setting.
     * @return bool True if the insert was successful, false otherwise.
     *
     * @example
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

        return $this->actions['insert']->execute($dataAction);
    }
}

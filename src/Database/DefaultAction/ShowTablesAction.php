<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class ShowTablesAction
 *
 * Retrieves a list of all tables in the database for a given repository.
 * This action dynamically builds and executes a query based on the database type.
 *
 * @package MiniCore\Database\DefaultAction
 *
 * @example
 * // Example usage to retrieve table names from MySQL:
 * $showTablesAction = new ShowTablesAction();
 * $tables = $showTablesAction->execute('mysql');
 * print_r($tables); // Output: Array of table names
 */
class ShowTablesAction extends AbstractAction implements ActionInterface
{
    /**
     * ShowTablesAction constructor.
     *
     * Initializes an action to retrieve table names from the database.
     *
     * @example
     * $showTablesAction = new ShowTablesAction();
     */
    public function __construct()
    {
        parent::__construct(
            'show_tables',
            ['mysql', 'postgresql']
        );
    }

    /**
     * Retrieves a list of all tables in the database.
     *
     * Constructs and executes a query based on the database type.
     *
     * @param string $repositoryName The repository (database type) from which to fetch tables.
     *                               Supported values: 'mysql', 'postgresql'.
     * @param DataAction|null $data Not used in this action.
     * @return array The list of table names.
     *
     * @throws \Exception If the provided repository type is unsupported.
     *
     * @example
     * // Retrieve all table names from a MySQL database
     * $showTablesAction = new ShowTablesAction();
     * $tables = $showTablesAction->execute('mysql');
     * print_r($tables);
     */
    public function execute(string $repositoryName, ?DataAction $data = null): array
    {
        $sql = match (strtolower($repositoryName)) {
            'mysql' => "SHOW TABLES",
            'postgresql' => "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'",
            default => throw new \Exception("Unsupported database type: {$repositoryName}"),
        };

        $result = RepositoryManager::query($repositoryName, $sql);
        return array_map('current', $result);
    }

    /**
     * Validate the provided data.
     *
     * Since this action does not require any input data, it always returns `true`.
     *
     * @param DataAction $data The data used for validation (not applicable in this case).
     * @return bool Always returns `true`.
     *
     * @example
     * $isValid = $showTablesAction->validate(new DataAction()); // true
     */
    public function validate(DataAction $data): bool
    {
        return true;
    }
}

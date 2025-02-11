<?php

namespace MiniCore\Database\DefaultAction;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Action\AbstractAction;
use MiniCore\Database\Action\ActionInterface;
use Minicore\Database\Repository\RepositoryManager;

/**
 * Class ShowTablesAction
 *
 * Retrieves a list of all tables in the database.
 *
 * @example
 * $showTablesAction = new ShowTablesAction();
 * $tables = $showTablesAction->execute('mysql_repository');
 * print_r($tables); // Array of table names
 */
class ShowTablesAction extends AbstractAction implements ActionInterface
{
    /**
     * ShowTablesAction constructor.
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
     * @param string $repositoryName The repository from which to fetch tables.
     * @param DataAction|null $data Not used in this action.
     * @return array The list of table names.
     *
     * @example
     * $tables = $showTablesAction->execute('mysql_repository');
     * print_r($tables);
     */
    public function execute(string $repositoryName, ?DataAction $data = null): array
    {
        $sql = match ($repositoryName) {
            'mysql' => "SHOW TABLES",
            'postgresql' => "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'",
            default => throw new \Exception("Unsupported database type: {$repositoryName}"),
        };

        $result = RepositoryManager::query($repositoryName, $sql);
        return array_map('current', $result);
    }

    public function validate(DataAction $data): bool
    {
        return true;
    }
}

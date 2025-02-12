<?php

namespace MiniCore\Tests\Database\Stub;

use MiniCore\Database\Repository\RepositoryInterface;
use MiniCore\Database\Table\TableManager;

/**
 * Class StubRepository
 *
 * A simple stub implementation of RepositoryInterface for testing purposes.
 */
class StubRepository implements RepositoryInterface
{
    private array $storage = [];

    /**
     * StubRepository constructor.
     *
     * @param array $config Configuration array (not used in stub).
     * @param array $tables List of tables (not used in stub).
     */
    public function __construct(array $config, array $tables = [])
    {
        // Simulated connection (does nothing)
    }

    public function getNameRepository(): string
    {
        return 'mysql';
    }

    public function connect(array $config): void
    {
        // Simulated connection (does nothing)
    }

    public function isConnected(): bool
    {
        return true;
    }

    public function getList(): TableManager
    {
        return new TableManager($this, []);
    }

    public function query(string $sql, array $params = []): array
    {
        return $this->storage;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->storage[] = $params;
        return true;
    }

    public function close(): void
    {
        $this->storage = [];
    }
}

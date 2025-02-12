<?php

namespace MiniCore\Tests\Database\Table;

use PHPUnit\Framework\TestCase;
use MiniCore\Tests\Database\Stub\StubRepository;
use MiniCore\Tests\Database\Stub\StubTable;
use MiniCore\Database\Table\TableManager;

/**
 * Class TableManagerTest
 *
 * Unit tests for TableManager class.
 */
class TableManagerTest extends TestCase
{
    private TableManager $tableManager;
    private StubRepository $repository;
    private StubTable $table;

    protected function setUp(): void
    {
        $this->repository = new StubRepository(['name' => 'mysql']);
        $this->table = new StubTable();
        $this->tableManager = new TableManager($this->repository, [$this->table]);
    }

    public function testAddTable()
    {
        $newTable = new StubTable();
        $this->tableManager->addTable($newTable);
        $this->assertCount(2, $this->tableManager->getTables());
    }

    public function testRemoveTable()
    {
        $this->tableManager->removeTable($this->table);
        $this->assertCount(0, $this->tableManager->getTables());
    }

    public function testGetTables()
    {
        $tables = $this->tableManager->getTables();
        $this->assertContains($this->table, $tables);
    }
}

<?php

namespace MiniCore\Tests\Database\Stub;

use MiniCore\Database\Table\AbstractTable;

/**
 * Class StubTable
 *
 * A stub implementation of AbstractTable for unit testing.
 */
class StubTable extends AbstractTable
{
    public function __construct()
    {
        parent::__construct(
            'stub_table',
            'mysql',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'name' => 'VARCHAR(255) NOT NULL',
            ]
        );
    }

    public function create(): void
    {
        // Simulated table creation
    }

    public function drop(): void
    {
        // Simulated table deletion
    }
}

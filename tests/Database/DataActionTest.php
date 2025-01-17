<?php

namespace MiniCore\Tests\Database;

use MiniCore\Database\DataAction;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the DataAction class.
 *
 * This test suite verifies the functionality of the DataAction class,
 * which is responsible for building and managing database query components,
 * such as columns, conditions, and parameters.
 *
 * Covered functionality:
 * - Adding and retrieving columns for queries.
 * - Adding and retrieving WHERE and ORDER BY conditions.
 * - Managing query parameters and ensuring proper overwriting of duplicate parameters.
 */
class DataActionTest extends TestCase
{
    /**
     * Tests adding and retrieving columns for a query.
     */
    public function testAddAndGetColumns(): void
    {
        $dataAction = new DataAction();

        $dataAction->addColumn('username');
        $dataAction->addColumn('email');

        $expectedColumns = ['username', 'email'];

        $this->assertEquals($expectedColumns, $dataAction->getColumns(), 'Columns were not added correctly.');
    }

    /**
     * Tests adding and retrieving a WHERE condition.
     */
    public function testAddAndGetWhereProperty(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);

        $expectedProperties = [
            ['type' => 'WHERE', 'condition' => 'id = :id'],
        ];

        $this->assertEquals($expectedProperties, $dataAction->getProperties(), 'WHERE condition was not added correctly.');
        $this->assertEquals(['id' => 1], $dataAction->getParameters(), 'WHERE parameters were not added correctly.');
    }

    /**
     * Tests adding multiple properties, such as WHERE and ORDER BY conditions.
     */
    public function testAddMultipleProperties(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
        $dataAction->addProperty('ORDER BY', 'created_at DESC');

        $expectedProperties = [
            ['type' => 'WHERE', 'condition' => 'status = :status'],
            ['type' => 'ORDER BY', 'condition' => 'created_at DESC'],
        ];

        $expectedParameters = ['status' => 'active'];

        $this->assertEquals($expectedProperties, $dataAction->getProperties(), 'Properties were not added correctly.');
        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Parameters were not added correctly.');
    }

    /**
     * Tests adding multiple query parameters.
     */
    public function testAddParameters(): void
    {
        $dataAction = new DataAction();

        $dataAction->addParameters(['id' => 1]);
        $dataAction->addParameters(['status' => 'active']);

        $expectedParameters = [
            'id' => 1,
            'status' => 'active',
        ];

        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Parameters were not added correctly.');
    }

    /**
     * Tests retrieving a specific property by its type.
     */
    public function testGetSpecificProperty(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
        $dataAction->addProperty('ORDER BY', 'created_at DESC');

        $properties = $dataAction->getProperties();

        $this->assertEquals('WHERE', $properties[0]['type'], 'WHERE property type mismatch.');
        $this->assertEquals('id = :id', $properties[0]['condition'], 'WHERE condition mismatch.');

        $this->assertEquals('ORDER BY', $properties[1]['type'], 'ORDER BY property type mismatch.');
        $this->assertEquals('created_at DESC', $properties[1]['condition'], 'ORDER BY condition mismatch.');
    }

    /**
     * Tests overwriting duplicate query parameters.
     */
    public function testAddDuplicateParameters(): void
    {
        $dataAction = new DataAction();

        $dataAction->addParameters(['id' => 1]);
        $dataAction->addParameters(['id' => 2]); // Overwrites the previous value

        $expectedParameters = ['id' => 2];

        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Duplicate parameter was not overwritten.');
    }
}

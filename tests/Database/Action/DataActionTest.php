<?php

namespace MiniCore\Tests\Database\Action;

use PHPUnit\Framework\TestCase;
use MiniCore\Database\Action\DataAction;

/**
 * Class DataActionTest
 *
 * Unit tests for the DataAction class.
 */
class DataActionTest extends TestCase
{
    public function testAddColumn()
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('username');

        $this->assertEquals(['username'], $dataAction->getColumns());
    }

    public function testAddProperty()
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);

        $expectedProperties = [
            ['type' => 'WHERE', 'condition' => 'id = :id']
        ];

        $this->assertEquals($expectedProperties, $dataAction->getProperties());
    }

    public function testAddParameters()
    {
        $dataAction = new DataAction();
        $dataAction->addParameters(['id' => 1, 'status' => 'active']);

        $expectedParams = ['id' => 1, 'status' => 'active'];

        $this->assertEquals($expectedParams, $dataAction->getParameters());
    }

    public function testGetProperty()
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);

        $expectedProperty = [['type' => 'WHERE', 'condition' => 'id = :id']];
        $this->assertEquals($expectedProperty, $dataAction->getProperty('WHERE'));
    }

    public function testAddParameter()
    {
        $dataAction = new DataAction();
        $dataAction->addParameter('key', 'value');

        $this->assertEquals(['key' => 'value'], $dataAction->getParameters());
    }
}

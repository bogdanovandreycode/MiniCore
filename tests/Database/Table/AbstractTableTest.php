<?php

namespace MiniCore\Tests\Database\Table;

use Exception;
use PHPUnit\Framework\TestCase;
use MiniCore\Database\Action\DataAction;
use MiniCore\Tests\Database\Stub\StubTable;
use MiniCore\Database\Action\ActionInterface;

/**
 * Class AbstractTableTest
 *
 * Unit tests for AbstractTable class.
 */
class AbstractTableTest extends TestCase
{
    private StubTable $table;

    protected function setUp(): void
    {
        $this->table = new StubTable();
    }

    public function testGetName()
    {
        $this->assertEquals('stub_table', $this->table->getName());
    }

    public function testGetSchemeToString()
    {
        $expectedSchema = "id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL";
        $this->assertEquals($expectedSchema, $this->table->getSchemeToString());
    }

    public function testAddAction()
    {
        $mockAction = $this->createMock(ActionInterface::class);
        $mockAction->method('getName')->willReturn('mock_action');

        // Добавляем, чтобы проверка репозитория всегда возвращала true
        $mockAction->method('checkAvailabilityRepository')->willReturn(true);

        $this->table->addAction($mockAction);
        $this->assertTrue($this->table->existAvailableAction('mock_action'), 'Action is not available');
    }


    public function testRemoveAction()
    {
        $mockAction = $this->createMock(ActionInterface::class);
        $mockAction->method('getName')->willReturn('mock_action');

        $this->table->addAction($mockAction);
        $this->table->removeAction('mock_action');

        $this->assertFalse($this->table->existAvailableAction('mock_action'));
    }

    public function testExecuteThrowsExceptionOnInvalidRepository()
    {
        $this->expectException(Exception::class);
        $dataAction = new DataAction();
        $this->table->execute('', 'insert', $dataAction);
    }
}

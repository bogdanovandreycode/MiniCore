<?php

namespace MiniCore\Tests\Database\Action;

use PHPUnit\Framework\TestCase;
use MiniCore\Database\Action\DataAction;
use MiniCore\Tests\Database\Stub\DummyAction;


/**
 * Class AbstractActionTest
 *
 * Unit tests for the AbstractAction class.
 */
class AbstractActionTest extends TestCase
{
    public function testGetName()
    {
        $action = new DummyAction('delete', ['users']);
        $this->assertEquals('delete', $action->getName());
    }

    public function testGetAvailableRepositories()
    {
        $action = new DummyAction('delete', ['users', 'posts']);
        $this->assertEquals(['users', 'posts'], $action->getAvailableRepositories());
    }

    public function testCheckAvailabilityRepository()
    {
        $action = new DummyAction('delete', ['users', 'posts']);

        $this->assertTrue($action->checkAvailabilityRepository('users'));
        $this->assertFalse($action->checkAvailabilityRepository('comments'));
    }

    public function testExecute()
    {
        $action = new DummyAction('delete', ['users']);
        $data = new DataAction();
        $data->addColumn('username');

        $result = $action->execute('users', $data);
        $this->assertInstanceOf(DataAction::class, $result);
    }

    public function testValidate()
    {
        $action = new DummyAction('delete', ['users']);

        $validData = new DataAction();
        $validData->addColumn('username');

        $invalidData = new DataAction();

        $this->assertTrue($action->validate($validData));
        $this->assertFalse($action->validate($invalidData));
    }
}

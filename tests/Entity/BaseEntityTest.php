<?php

namespace MiniCore\Tests\Entity;

use PHPUnit\Framework\TestCase;
use MiniCore\Entity\BaseEntity;

/**
 * Unit tests for the BaseEntity class.
 *
 * This test suite verifies the core functionality of the BaseEntity class,
 * which serves as a foundation for handling entity data in the application.
 *
 * Covered functionality:
 * - Getting and setting entity properties.
 * - Checking if properties are set.
 * - Unsetting properties.
 * - Converting the entity to an array.
 * - Populating the entity from an array.
 * - Managing the entity's ID.
 */
class TestEntity extends BaseEntity {}

class BaseEntityTest extends TestCase
{
    /**
     * @var TestEntity Instance of the entity used for testing.
     */
    private TestEntity $entity;

    /**
     * Initializes the entity with default properties before each test.
     */
    protected function setUp(): void
    {
        $this->entity = new TestEntity(['name' => 'John', 'email' => 'john@example.com']);
    }

    /**
     * Tests getting and setting entity properties.
     */
    public function testGetAndSetProperty()
    {
        $this->assertEquals('John', $this->entity->name);

        $this->entity->name = 'Jane';
        $this->assertEquals('Jane', $this->entity->name);
    }

    /**
     * Tests checking if a property is set.
     */
    public function testIssetProperty()
    {
        $this->assertTrue(isset($this->entity->email));
        $this->assertFalse(isset($this->entity->age));
    }

    /**
     * Tests unsetting a property.
     */
    public function testUnsetProperty()
    {
        unset($this->entity->email);
        $this->assertFalse(isset($this->entity->email));
    }

    /**
     * Tests converting the entity to an associative array.
     */
    public function testToArray()
    {
        $expected = ['name' => 'John', 'email' => 'john@example.com'];
        $this->assertEquals($expected, $this->entity->toArray());
    }

    /**
     * Tests populating the entity from an associative array.
     */
    public function testFromArray()
    {
        $this->entity->fromArray(['name' => 'Alice', 'email' => 'alice@example.com']);
        $this->assertEquals('Alice', $this->entity->name);
    }

    /**
     * Tests setting and getting the entity's ID.
     */
    public function testGetAndSetId()
    {
        $this->assertNull($this->entity->getId());

        $this->entity->setId(42);
        $this->assertEquals(42, $this->entity->getId());
    }
}

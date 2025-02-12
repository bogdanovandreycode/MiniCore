<?php

namespace MiniCore\Tests\Database\Repository;

use Exception;
use PHPUnit\Framework\TestCase;
use MiniCore\Tests\Database\Stub\StubRepository;
use MiniCore\Database\Repository\RepositoryManager;

/**
 * Class RepositoryManagerTest
 *
 * Unit tests for RepositoryManager class using a stub repository.
 */
class RepositoryManagerTest extends TestCase
{
    /**
     * Stub repository instance.
     *
     * @var StubRepository
     */
    private StubRepository $stubRepository;

    protected function setUp(): void
    {
        $this->stubRepository = new StubRepository([], []);
        RepositoryManager::addRepository($this->stubRepository);
    }

    /**
     * Test repository registration.
     */
    public function testAddRepository()
    {
        $this->assertInstanceOf(
            StubRepository::class,
            RepositoryManager::getRepository('mysql')
        );
    }

    /**
     * Test getting a registered repository.
     */
    public function testGetRepository()
    {
        $repository = RepositoryManager::getRepository('mysql');
        $this->assertSame($this->stubRepository, $repository);
    }

    /**
     * Test getting an unregistered repository throws an exception.
     */
    public function testGetRepositoryThrowsException()
    {
        $this->expectException(Exception::class);
        RepositoryManager::getRepository('unknown_repo');
    }

    /**
     * Test removing a repository.
     */
    public function testRemoveRepository()
    {
        RepositoryManager::removeRepository('mysql');
        $this->expectException(Exception::class);
        RepositoryManager::getRepository('mysql');
    }

    /**
     * Test removing a non-existent repository throws an exception.
     */
    public function testRemoveNonExistentRepository()
    {
        $this->expectException(Exception::class);
        RepositoryManager::removeRepository('unknown_repo');
    }

    /**
     * Test executing a SELECT query.
     */
    public function testQuery()
    {
        $this->stubRepository->execute('INSERT INTO users VALUES (:id, :name)', ['id' => 1, 'name' => 'John Doe']);

        $result = RepositoryManager::query('mysql', 'SELECT * FROM users WHERE id = :id', ['id' => 1]);
        $this->assertEquals([['id' => 1, 'name' => 'John Doe']], $result);
    }

    /**
     * Test executing an INSERT/UPDATE/DELETE query.
     */
    public function testExecute()
    {
        $result = RepositoryManager::execute('mysql', 'UPDATE users SET name = :name WHERE id = :id', ['name' => 'Jane Doe', 'id' => 1]);
        $this->assertTrue($result);
    }
}

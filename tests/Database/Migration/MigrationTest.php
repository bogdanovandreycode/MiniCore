<?php

namespace MiniCore\Tests\Database\Migration;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use MiniCore\Database\Migration\MigrationStatus;
use MiniCore\Database\Migration\MigrationManager;
use MiniCore\Database\Migration\AbstractMigration;

/**
 * Class MigrationTest
 *
 * Tests for AbstractMigration and MigrationManager classes.
 */
class MigrationTest extends TestCase
{
    /**
     * Test setting and getting migration status.
     */
    public function testMigrationStatus()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return true;
            }
        };

        // Check default status
        $this->assertEquals(MigrationStatus::UNAPPLIED, $migration->getStatus());

        // Change and verify status
        $migration->setStatus(MigrationStatus::RUNNING);
        $this->assertEquals(MigrationStatus::RUNNING, $migration->getStatus());

        $migration->setStatus(MigrationStatus::COMPLETED);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());
    }

    /**
     * Test successful migration execution.
     */
    public function testMigrationUp()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return true;
            }
        };

        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());
    }

    /**
     * Test failed migration execution.
     */
    public function testMigrationUpFails()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return false;
            }
            public function down(): bool
            {
                return true;
            }
        };

        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::FAILED, $migration->getStatus());
    }

    /**
     * Test rollback of completed migration.
     */
    public function testMigrationRollback()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return true;
            }
        };

        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());

        MigrationManager::rollback($migration);
        $this->assertEquals(MigrationStatus::UNAPPLIED, $migration->getStatus());
    }

    /**
     * Test failed rollback execution.
     */
    public function testMigrationRollbackFails()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return false;
            }
        };

        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());

        MigrationManager::rollback($migration);
        $this->assertEquals(MigrationStatus::FAILED, $migration->getStatus());
    }

    /**
     * Test that migration is not executed again if already completed.
     */
    public function testMigrationDoesNotRepeat()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return true;
            }
        };

        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());

        // Try executing migration again, it should remain COMPLETED
        MigrationManager::migrate($migration);
        $this->assertEquals(MigrationStatus::COMPLETED, $migration->getStatus());
    }

    /**
     * Test registering migrations in the manager.
     */
    public function testMigrationRegistration()
    {
        $migration = new class extends AbstractMigration {
            public function up(): bool
            {
                return true;
            }
            public function down(): bool
            {
                return true;
            }
        };

        MigrationManager::addMigration($migration);
        $this->assertContains($migration, $this->getPrivateProperty(MigrationManager::class, 'migrations'));
    }

    /**
     * Helper function to access private properties in tests.
     *
     * @param string $className The class name.
     * @param string $property The property name.
     * @return mixed The property value.
     */
    private function getPrivateProperty(string $className, string $property)
    {
        $reflection = new ReflectionClass($className);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue();
    }
}

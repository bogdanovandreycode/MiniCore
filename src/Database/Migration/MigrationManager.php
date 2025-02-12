<?php

namespace MiniCore\Database\Migration;

/**
 * Class MigrationManager
 *
 * Manages the execution and rollback of database migrations.
 * Provides methods to register migrations, apply them, and roll them back.
 *
 * @package MiniCore\Database\Migration
 *
 * @example
 * // Example usage:
 * $migration = new CreateUsersTable();
 * MigrationManager::addMigration($migration);
 * MigrationManager::migrate($migration);
 */
class MigrationManager
{
    /**
     * List of registered migrations.
     *
     * @var MigrationInterface[]
     */
    private static array $migrations = [];

    /**
     * Register a migration.
     *
     * @param MigrationInterface $migration The migration to register.
     * @return void
     *
     * @example
     * $migration = new CreateUsersTable();
     * MigrationManager::addMigration($migration);
     */
    public static function addMigration(MigrationInterface $migration): void
    {
        self::$migrations[] = $migration;
    }

    /**
     * Apply a migration.
     *
     * Executes the `up()` method of the migration and updates its status.
     * If the migration is already completed, it will not be executed again.
     *
     * @param MigrationInterface $migration The migration to apply.
     * @return void
     *
     * @example
     * $migration = new CreateUsersTable();
     * MigrationManager::migrate($migration);
     */
    public static function migrate(MigrationInterface $migration): void
    {
        if ($migration->getStatus() === MigrationStatus::COMPLETED) {
            return;
        }

        $migration->setStatus(MigrationStatus::RUNNING);

        if ($migration->up()) {
            $migration->setStatus(MigrationStatus::COMPLETED);
            return;
        }

        $migration->setStatus(MigrationStatus::FAILED);
    }

    /**
     * Rollback a migration.
     *
     * Executes the `down()` method of the migration and updates its status.
     * If the migration has not been applied, it will not be rolled back.
     *
     * @param MigrationInterface $migration The migration to rollback.
     * @return void
     *
     * @example
     * $migration = new CreateUsersTable();
     * MigrationManager::rollback($migration);
     */
    public static function rollback(MigrationInterface $migration): void
    {
        if ($migration->getStatus() !== MigrationStatus::COMPLETED) {
            return;
        }

        $migration->setStatus(MigrationStatus::RUNNING);

        if ($migration->down()) {
            $migration->setStatus(MigrationStatus::UNAPPLIED);
            return;
        }

        $migration->setStatus(MigrationStatus::FAILED);
    }
}

<?php

namespace MiniCore\Database\Migration;

use MiniCore\Database\Migration\MigrationStatus;
use MiniCore\Database\Migration\MigrationInterface;

/**
 * Class AbstractMigration
 *
 * Provides a base class for database migrations.
 * Migrations are used to modify the database schema, applying (`up()`) or reverting (`down()`) changes.
 * This class also manages the migration status.
 *
 * @package MiniCore\Database\Migration
 *
 * @example
 * // Example usage:
 * class CreateUsersTable extends AbstractMigration
 * {
 *     public function up(): bool
 *     {
 *         // Logic to apply the migration (e.g., create a table)
 *         return true;
 *     }
 *
 *     public function down(): bool
 *     {
 *         // Logic to revert the migration (e.g., drop a table)
 *         return true;
 *     }
 * }
 *
 * $migration = new CreateUsersTable();
 * $migration->setStatus(MigrationStatus::APPLIED);
 * echo $migration->getStatus(); // Output: APPLIED
 */
abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @var MigrationStatus The current status of the migration.
     */
    protected MigrationStatus $status = MigrationStatus::UNAPPLIED;

    /**
     * Get the current migration status.
     *
     * @return MigrationStatus The status of the migration.
     *
     * @example
     * $status = $migration->getStatus();
     * echo $status; // Output: UNAPPLIED, APPLIED, or FAILED
     */
    public function getStatus(): MigrationStatus
    {
        return $this->status;
    }

    /**
     * Set the migration status.
     *
     * @param MigrationStatus $status The new migration status.
     * @return void
     *
     * @example
     * $migration->setStatus(MigrationStatus::APPLIED);
     */
    public function setStatus(MigrationStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Apply the migration.
     *
     * This method should contain the logic to modify the database schema (e.g., create tables, add indexes).
     *
     * @return bool True if the migration was successfully applied, false otherwise.
     *
     * @example
     * $success = $migration->up();
     * if ($success) {
     *     echo "Migration applied successfully.";
     * }
     */
    abstract public function up(): bool;

    /**
     * Revert the migration.
     *
     * This method should contain the logic to undo the changes made by `up()`.
     *
     * @return bool True if the migration was successfully reverted, false otherwise.
     *
     * @example
     * $success = $migration->down();
     * if ($success) {
     *     echo "Migration reverted successfully.";
     * }
     */
    abstract public function down(): bool;
}

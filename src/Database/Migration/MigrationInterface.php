<?php

namespace MiniCore\Database\Migration;

use MiniCore\Database\Migration\MigrationStatus;

/**
 * Interface MigrationInterface
 *
 * Defines the contract for database migrations.
 * Implement this interface to create and manage database schema changes,
 * such as creating or dropping tables, adding columns, or modifying indexes.
 *
 * @package MiniCore\Database\Migration
 *
 * @example
 * // Example of implementing a migration:
 * class CreateUsersTable implements MigrationInterface
 * {
 *     public function up(): bool
 *     {
 *         $sql = "CREATE TABLE users (
 *             id INT AUTO_INCREMENT PRIMARY KEY,
 *             username VARCHAR(255) NOT NULL,
 *             email VARCHAR(255) UNIQUE NOT NULL,
 *             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 *         )";
 *
 *         return DataBase::execute($sql);
 *     }
 *
 *     public function down(): bool
 *     {
 *         $sql = "DROP TABLE IF EXISTS users";
 *         return DataBase::execute($sql);
 *     }
 *
 *     public function getStatus(): MigrationStatus
 *     {
 *         return MigrationStatus::UNAPPLIED;
 *     }
 *
 *     public function setStatus(MigrationStatus $status): void
 *     {
 *         // Set migration status
 *     }
 * }
 */
interface MigrationInterface
{
    /**
     * Apply the migration.
     *
     * This method should contain logic for applying the migration, such as:
     * - Creating new tables
     * - Adding new columns
     * - Creating indexes
     *
     * @return bool True if the migration was successful, false otherwise.
     *
     * @example
     * $migration = new CreateUsersTable();
     * if ($migration->up()) {
     *     echo "Migration applied successfully.";
     * }
     */
    public function up(): bool;

    /**
     * Rollback the migration.
     *
     * This method should contain logic to reverse the migration, such as:
     * - Dropping tables
     * - Removing columns
     * - Deleting indexes
     *
     * @return bool True if the rollback was successful, false otherwise.
     *
     * @example
     * $migration = new CreateUsersTable();
     * if ($migration->down()) {
     *     echo "Migration reverted successfully.";
     * }
     */
    public function down(): bool;

    /**
     * Get the current migration status.
     *
     * @return MigrationStatus The current status of the migration.
     *
     * @example
     * $status = $migration->getStatus();
     * echo $status; // Output: UNAPPLIED, APPLIED, or FAILED
     */
    public function getStatus(): MigrationStatus;

    /**
     * Set the migration status.
     *
     * @param MigrationStatus $status The new migration status.
     * @return void
     *
     * @example
     * $migration->setStatus(MigrationStatus::APPLIED);
     */
    public function setStatus(MigrationStatus $status): void;
}

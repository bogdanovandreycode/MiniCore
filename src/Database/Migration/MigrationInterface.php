<?php

namespace MiniCore\Database\Migration;

use MiniCore\Database\Migration\MigrationStatus;

/**
 * Interface Migration
 *
 * Defines the contract for database migrations.
 * Implement this interface to create and manage database schema changes,
 * such as creating or dropping tables, adding columns, or modifying indexes.
 *
 * @package MiniCore\Database
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
     * public function up(): bool {
     *     $sql = "CREATE TABLE users (
     *         id INT AUTO_INCREMENT PRIMARY KEY,
     *         username VARCHAR(255) NOT NULL,
     *         email VARCHAR(255) UNIQUE NOT NULL,
     *         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     *     )";
     *     
     *     return DataBase::execute($sql);
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
     * public function down(): bool {
     *     $sql = "DROP TABLE IF EXISTS users";
     *     return DataBase::execute($sql);
     * }
     */
    public function down(): bool;

    public function getStatus(): MigrationStatus;
    public function setStatus(MigrationStatus $status): void;
}

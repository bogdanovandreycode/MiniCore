<?php

namespace MiniCore\Database\Migration;

/**
 * Enum MigrationStatus
 *
 * Represents the different states of a database migration.
 * This enum is used to track the progress of migrations during execution.
 *
 * Possible statuses:
 * - `UNAPPLIED`: The migration has not been applied yet.
 * - `PENDING`: The migration is scheduled to be applied but has not started yet.
 * - `RUNNING`: The migration is currently being executed.
 * - `COMPLETED`: The migration was successfully applied.
 * - `FAILED`: The migration encountered an error and failed.
 *
 * @package MiniCore\Database\Migration
 *
 * @example
 * // Example usage:
 * $status = MigrationStatus::UNAPPLIED;
 * echo $status->value; // Output: unapplied
 *
 * // Updating migration status
 * $migration->setStatus(MigrationStatus::COMPLETED);
 */
enum MigrationStatus: string
{
    /**
     * The migration has not been applied yet.
     */
    case UNAPPLIED = 'unapplied';

    /**
     * The migration is scheduled to be applied but has not started yet.
     */
    case PENDING = 'pending';

    /**
     * The migration is currently being executed.
     */
    case RUNNING = 'running';

    /**
     * The migration was successfully applied.
     */
    case COMPLETED = 'completed';

    /**
     * The migration encountered an error and failed.
     */
    case FAILED = 'failed';
}

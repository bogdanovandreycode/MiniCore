<?php

namespace Vendor\Undermarket\Core\Database;

interface Migration
{
    /**
     * Apply the migration (e.g., create tables, add columns).
     * @return bool True if the migration was successful, false otherwise.
     */
    public function up(): bool;

    /**
     * Rollback the migration (e.g., drop tables, remove columns).
     * @return bool True if the rollback migration was successful, false otherwise.
     */
    public function down(): bool;
}

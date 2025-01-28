<?php

namespace MiniCore\Database\Migration;

use MiniCore\Database\Migration\MigrationStatus;
use MiniCore\Database\Migration\MigrationInterface;

abstract class AbstractMigration implements MigrationInterface
{
    protected MigrationStatus $status = MigrationStatus::UNAPPLIED;

    public function getStatus(): MigrationStatus
    {
        return self::$status;
    }

    public function setStatus(MigrationStatus $status): void
    {
        self::$status = $status;
    }

    abstract public function up(): bool;
    abstract public function down(): bool;
}

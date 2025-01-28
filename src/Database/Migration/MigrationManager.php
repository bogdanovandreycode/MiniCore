<?php

namespace MiniCore\Database\Migration;

class MigrationManager
{
    /**
     * Summary of migratrions
     * @var MigrationInterface[]
     */
    private static array $migratrions = [];

    public static function addMegration(MigrationInterface $migration): void
    {
        self::$migratrions[] = $migration;
    }

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

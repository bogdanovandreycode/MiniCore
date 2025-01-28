<?php

namespace MiniCore\Database\Migration;

enum MigrationStatus: string
{
    case UNAPPLIED = 'unapplied';
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}

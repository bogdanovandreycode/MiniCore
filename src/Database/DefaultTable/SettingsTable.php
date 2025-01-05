<?php

namespace Vendor\Undermarket\Models;

use MiniCore\Database\Table;

class SettingsTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'settings',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'key_name' => 'VARCHAR(255) NOT NULL UNIQUE',
                'value' => 'TEXT NOT NULL',
            ]
        );
    }
}

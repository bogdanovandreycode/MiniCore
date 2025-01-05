<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;

class RolesTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'roles',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'name' => 'VARCHAR(255) NOT NULL UNIQUE',
                'permissions' => 'JSON NOT NULL',
            ]
        );
    }
}

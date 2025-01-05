<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Table;

class UsersTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'users',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'username' => 'VARCHAR(255) NOT NULL',
                'email' => 'VARCHAR(255) UNIQUE NOT NULL',
                'password_hash' => 'VARCHAR(255) NOT NULL',
                'role_id' => 'INT NOT NULL',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ]
        );
    }
}

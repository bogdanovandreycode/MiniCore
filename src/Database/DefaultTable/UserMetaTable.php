<?php

namespace Vendor\Undermarket\Models;

use MiniCore\Database\Table;
use MiniCore\Database\DataAction;

class UsersTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'usermeta',
            [
                'meta_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'user_id' => 'INT NOT NULL',
                'meta_key' => 'VARCHAR(255) NOT NULL',
                'meta_value' => 'TEXT',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ]
        );
    }

    public function getMeta(string $key, bool $isSingle = true)
    {
        $dataAction = new DataAction;
    }
}

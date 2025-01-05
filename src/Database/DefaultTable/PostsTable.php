<?php

namespace Vendor\Undermarket\Models;

use MiniCore\Database\Table;

class PostsTable extends Table
{
    public function __construct()
    {
        parent::__construct(
            'posts',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'type' => 'VARCHAR(255) NOT NULL',
                'title' => 'VARCHAR(255) NULL',
                'content' => 'longtext NULL',
                'author_id' => 'INT NOT NULL',
                'url' => 'VARCHAR(1024) NULL',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ]
        );
    }
}

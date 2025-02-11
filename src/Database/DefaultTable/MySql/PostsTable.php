<?php

namespace MiniCore\Database\DefaultTable;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class PostsTable
 *
 * This class defines the structure and behavior of the `posts` table in the database.
 * It manages different types of content such as articles, pages, or custom post types.
 *
 * @example
 * // Example of usage:
 * $postsTable = new PostsTable();
 * // Use select action
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('username');
 * $dataAction->addProperty('WHERE', 'type = :type', ['type' => 'post']);
 * $postsTable->actions['select']->execute($dataAction);
 */
class PostsTable extends AbstractTable
{
    /**
     * PostsTable constructor.
     *
     * Initializes the `posts` table with its structure and columns.
     */
    public function __construct()
    {
        parent::__construct(
            'posts',
            'mysql',
            [
                'id'         => 'INT AUTO_INCREMENT PRIMARY KEY',    // Unique identifier for each post
                'type'       => 'VARCHAR(255) NOT NULL',             // Post type (e.g., article, page)
                'title'      => 'VARCHAR(255) NULL',                 // Title of the post
                'content'    => 'LONGTEXT NULL',                    // Post content
                'author_id'  => 'INT NOT NULL',                      // ID of the post author
                'url'        => 'VARCHAR(1024) NULL',               // URL-friendly slug or link
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',  // Creation timestamp
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', // Last update timestamp
            ]
        );
    }

    /**
     * Retrieve a post by its ID.
     *
     * @param int $postId The ID of the post.
     * @return array|null The post data or null if not found.
     *
     * @example
     * $post = $postsTable->getPostById(1);
     */
    public function getPostById(int $postId): ?array
    {
        $dataAction = new DataAction();
        $dataAction->addProperty('WHERE', 'id = :id', ['id' => $postId]);
        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);
        return $result[0] ?? null;
    }
}

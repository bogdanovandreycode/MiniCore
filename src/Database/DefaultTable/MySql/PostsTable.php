<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class PostsTable
 *
 * Represents the `posts` table in the database.
 * This class defines the structure and behavior of the posts, including different types of content such as articles, pages, or custom post types.
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $postsTable = new PostsTable();
 *
 * // Retrieve a post by ID
 * $post = $postsTable->getPostById(1);
 * print_r($post);
 *
 * // Select posts with a specific type
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('title');
 * $dataAction->addProperty('WHERE', 'type = :type', ['type' => 'post']);
 * $posts = $postsTable->actions['select']->execute('mysql', $dataAction);
 * print_r($posts);
 */
class PostsTable extends AbstractTable
{
    /**
     * PostsTable constructor.
     *
     * Initializes the `posts` table with its structure and columns.
     *
     * @example
     * $postsTable = new PostsTable();
     */
    public function __construct()
    {
        parent::__construct(
            'posts',
            'mysql',
            [
                'id'         => 'INT AUTO_INCREMENT PRIMARY KEY',   // Unique identifier for each post
                'type'       => 'VARCHAR(255) NOT NULL',            // Post type (e.g., article, page)
                'title'      => 'VARCHAR(255) NULL',                // Title of the post
                'content'    => 'LONGTEXT NULL',                   // Post content
                'author_id'  => 'INT NOT NULL',                     // ID of the post author
                'url'        => 'VARCHAR(1024) NULL',              // URL-friendly slug or link
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP', // Creation timestamp
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', // Last update timestamp
            ]
        );
    }

    /**
     * Retrieve a post by its ID.
     *
     * @param int $postId The ID of the post to retrieve.
     * @return array|null The post data as an associative array, or null if the post is not found.
     *
     * @example
     * // Retrieve a post by its ID
     * $post = $postsTable->getPostById(1);
     * print_r($post);
     */
    public function getPostById(int $postId): ?array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('id');
        $dataAction->addColumn('type');
        $dataAction->addColumn('title');
        $dataAction->addColumn('content');
        $dataAction->addColumn('author_id');
        $dataAction->addColumn('url');
        $dataAction->addColumn('created_at');
        $dataAction->addColumn('updated_at');

        $dataAction->addProperty('WHERE', 'id = :id', ['id' => $postId]);

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

        return $result[0] ?? null;
    }

    /**
     * Retrieve posts by type.
     *
     * @param string $type The type of posts to retrieve (e.g., 'article', 'page').
     * @return array List of posts matching the type.
     *
     * @example
     * // Retrieve all posts of type 'article'
     * $articles = $postsTable->getPostsByType('article');
     * print_r($articles);
     */
    public function getPostsByType(string $type): array
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('id');
        $dataAction->addColumn('title');
        $dataAction->addColumn('content');
        $dataAction->addColumn('author_id');
        $dataAction->addColumn('url');
        $dataAction->addColumn('created_at');
        $dataAction->addColumn('updated_at');

        $dataAction->addProperty('WHERE', 'type = :type', ['type' => $type]);

        return $this->actions['select']->execute($this->repositoryName, $dataAction);
    }
}

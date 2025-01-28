<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Table;
use MiniCore\Database\Action\DataAction;

/**
 * Class PostMetaTable
 *
 * This class manages metadata for posts, allowing retrieval, updating, and insertion of custom post data.
 *
 * @example
 * // Example of usage:
 * $postMetaTable = new PostMetaTable();
 * 
 * // Use select action
 * $dataAction = new DataAction();
 * $dataAction->addColumn('id');
 * $dataAction->addColumn('username');
 * $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
 * $postMetaTable->actions['select']->execute($dataAction);
 * 
 * // Add new metadata
 * $postMetaTable->addMeta(1, 'featured', 'true');
 * 
 * // Update metadata
 * $postMetaTable->updateMeta(1, 'featured', 'false');
 * 
 * // Retrieve metadata
 * $metaValue = $postMetaTable->getMeta('featured');
 */
class PostMetaTable extends Table
{
    /**
     * PostMetaTable constructor.
     *
     * Initializes the `postmeta` table with its schema.
     */
    public function __construct()
    {
        parent::__construct(
            'postmeta',
            [
                'meta_id'     => 'INT AUTO_INCREMENT PRIMARY KEY',
                'post_id'     => 'INT NOT NULL',
                'meta_key'    => 'VARCHAR(255) NOT NULL',
                'meta_value'  => 'TEXT',
                'created_at'  => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at'  => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ]
        );
    }

    /**
     * Retrieve metadata for a post by key.
     *
     * @param string $key The metadata key to search for.
     * @param bool $isSingle Whether to return a single value or an associative array of results.
     * @return mixed The metadata value or an associative array of key-value pairs.
     *
     * @example
     * // Get a single meta value
     * $metaValue = $postMetaTable->getMeta('featured');
     * 
     * // Get all meta values with the same key
     * $allMetaValues = $postMetaTable->getMeta('featured', false);
     */
    public function getMeta(string $key, bool $isSingle = true)
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');
        $dataAction->addProperty('WHERE', 'meta_key = :meta_key', ['meta_key' => $key]);

        $result = $this->actions['select']->execute($dataAction);

        if ($result) {
            return $isSingle
                ? ($result[0]['meta_value'] ?? null)
                : array_column($result, 'meta_value', 'meta_key');
        }

        return $isSingle ? null : [];
    }

    /**
     * Update existing metadata or add it if it doesn't exist.
     *
     * @param int $postId The ID of the post.
     * @param string $key The metadata key.
     * @param string $value The metadata value.
     * @return bool True if the operation was successful, false otherwise.
     *
     * @example
     * // Update or insert metadata
     * $postMetaTable->updateMeta(1, 'featured', 'true');
     */
    public function updateMeta(int $postId, string $key, string $value): bool
    {
        $existingMeta = $this->getMeta($key, true);

        if ($existingMeta === null) {
            return $this->addMeta($postId, $key, $value);
        }

        $dataAction = new DataAction();
        $dataAction->addColumn('meta_value');
        $dataAction->addParameters(['meta_value' => $value]);

        $dataAction->addProperty('WHERE', 'meta_key = :meta_key AND post_id = :post_id', [
            'meta_key' => $key,
            'post_id'  => $postId,
        ]);

        return $this->actions['update']->execute($dataAction);
    }

    /**
     * Insert new metadata for a post.
     *
     * @param int $postId The ID of the post.
     * @param string $key The metadata key.
     * @param string $value The metadata value.
     * @return bool True if the insertion was successful, false otherwise.
     *
     * @example
     * // Add new metadata
     * $postMetaTable->addMeta(1, 'featured', 'true');
     */
    public function addMeta(int $postId, string $key, string $value): bool
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('post_id');
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');

        $dataAction->addParameters([
            'post_id'    => $postId,
            'meta_key'   => $key,
            'meta_value' => $value,
        ]);

        return $this->actions['insert']->execute($dataAction);
    }
}

<?php

namespace MiniCore\Database\DefaultTable\MySql;

use MiniCore\Database\Action\DataAction;
use MiniCore\Database\Table\AbstractTable;

/**
 * Class PostMetaTable
 *
 * Manages metadata for posts, allowing retrieval, updating, and insertion of custom post data.
 * This table stores metadata as key-value pairs associated with a specific post.
 *
 * @package MiniCore\Database\DefaultTable\MySql
 *
 * @example
 * // Example usage:
 * $postMetaTable = new PostMetaTable();
 *
 * // Retrieve metadata
 * $metaValue = $postMetaTable->getMeta('featured');
 *
 * // Add new metadata
 * $postMetaTable->addMeta(1, 'featured', 'true');
 *
 * // Update metadata
 * $postMetaTable->updateMeta(1, 'featured', 'false');
 */
class PostMetaTable extends AbstractTable
{
    /**
     * PostMetaTable constructor.
     *
     * Initializes the `postmeta` table with its schema.
     *
     * @example
     * $postMetaTable = new PostMetaTable();
     */
    public function __construct()
    {
        parent::__construct(
            'postmeta',
            'mysql',
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
     * @param bool $isSingle Whether to return a single value (default: true).
     * @return mixed The metadata value if `$isSingle` is `true`, or an associative array of key-value pairs otherwise.
     *
     * @example
     * // Get a single meta value
     * $metaValue = $postMetaTable->getMeta('featured');
     *
     * // Get all meta values with the same key
     * $allMetaValues = $postMetaTable->getMeta('featured', false);
     */
    public function getMeta(string $key, bool $isSingle = true): mixed
    {
        $dataAction = new DataAction();
        $dataAction->addColumn('meta_key');
        $dataAction->addColumn('meta_value');
        $dataAction->addProperty('WHERE', 'meta_key = :meta_key', ['meta_key' => $key]);

        $result = $this->actions['select']->execute($this->repositoryName, $dataAction);

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

        return $this->actions['update']->execute($this->repositoryName, $dataAction);
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

        return $this->actions['insert']->execute($this->repositoryName, $dataAction);
    }
}

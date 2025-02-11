<?php

namespace Minicore\Database\Repository;

use Exception;
use MiniCore\Database\Repository\RepositoryInterface;

class RepositoryManager
{
    /**
     * Repository list
     * 
     * @var RepositoryInterface[]
     */
    private static $repositories;

    public static function addRepository(RepositoryInterface $repository): void
    {
        self::$repositories[$repository->getNameRepository()] = $repository;
    }

    private static function validateNameRepository(string $name): void
    {
        if (empty($name) || !isset(self::$repositories[$name])) {
            throw new Exception("Repository name not found", 1);
        }
    }

    public static function getRepository(string $name): RepositoryInterface
    {
        self::validateNameRepository($name);
        return self::$repositories[$name];
    }

    public static function removeRepository(string $name): void
    {
        self::validateNameRepository($name);
        unset(self::$repositories[$name]);
    }

    public static function query(string $nameRepository, string $sql, array $params = []): array
    {
        self::validateNameRepository($nameRepository);

        try {
            $result = self::$repositories[$nameRepository]->query($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Error during query execution: " . $e->getMessage(), 1, $e);
        }

        return $result;
    }

    public static function execute(string $nameRepository, string $sql, array $params = []): bool
    {
        self::validateNameRepository($nameRepository);

        try {
            $result = self::$repositories[$nameRepository]->execute($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Error during query execution: " . $e->getMessage(), 1, $e);
        }

        return $result;
    }
}

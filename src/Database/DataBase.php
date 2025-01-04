<?php

namespace MiniCore\Database;

use PDO;
use PDOException;

class DataBase
{
    private static PDO $connection;

    public static function getConnection(): PDO
    {
        return self::$connection;
    }

    public static function setConnection(string $host, string $dbname, string $user, string $password, string $charset = 'utf8mb4')
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        try {
            self::$connection = new PDO($dsn, $user, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::$connection->prepare($sql);
        return $stmt->execute($params);
    }
}

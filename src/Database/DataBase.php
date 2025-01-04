<?php

namespace MiniCore\Database;

use PDO;
use PDOException;

class DataBase
{
    private static PDO $connection;
    private static array $tables = [];

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

    public static function addTable(Table $table): void
    {
        self::$tables[] = $table;
    }

    public static function removeTable(Table $table): void
    {
        $index = array_search($table, self::$tables, true);

        if ($index !== false) {
            unset(self::$tables[$index]);
            self::$tables = array_values(self::$tables); // Reindex the array
        }
    }

    public static function createTables(): void
    {
        foreach (self::$tables as $table) {
            if (!$table->exist()) {
                $table->create();
            }
        }
    }

    public static function dropTables(): void
    {
        foreach (self::$tables as $table) {
            if ($table->exist()) {
                $table->drop();
            }
        }
    }

    public static function migrate(Migration $migration): void
    {
        if ($migration->up()) {
            echo "Migration applied successfully.\n";
        } else {
            echo "Migration failed.\n";
        }
    }

    public static function rollback(Migration $migration): void
    {
        if ($migration->down()) {
            echo "Rollback migration applied successfully.\n";
        } else {
            echo "Rollback migration failed.\n";
        }
    }
}

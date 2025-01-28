<?php

namespace MiniCore\Database\Repository;

use MiniCore\Database\Table\AbstractTable;

interface RepositoryInterface
{
    /**
     * Установить соединение с базой данных.
     *
     * @param array $config Конфигурация подключения.
     * @return void
     */
    public static function connect(array $config): void;

    /**
     * Проверить активность соединения.
     *
     * @return bool True, если соединение активно.
     */
    public static function isConnected(): bool;

    /**
     * Выполнить SQL-запрос SELECT.
     *
     * @param string $sql SQL-запрос.
     * @param array $params Параметры для подготовленного запроса.
     * @return array Результат выполнения запроса.
     */
    public static function query(string $sql, array $params = []): array;

    /**
     * Выполнить SQL-запрос INSERT, UPDATE, DELETE.
     *
     * @param string $sql SQL-запрос.
     * @param array $params Параметры для подготовленного запроса.
     * @return bool Успешность выполнения запроса.
     */
    public static function execute(string $sql, array $params = []): bool;

    /**
     * Закрыть соединение с базой данных.
     *
     * @return void
     */
    public static function close(): void;

    public static function getNameRepository(): string;

    public static function addTable(AbstractTable $table): void;

    public static function removeTable(AbstractTable $table): void;

    public static function createTables(): void;

    public static function dropTables(): void;
}

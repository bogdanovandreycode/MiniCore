<?php

namespace MiniCore\Database\Repository;

use MiniCore\Database\Table\TableManager;

interface RepositoryInterface
{
    public function __construct(array $config, array $tables = [],);
    /**
     * Установить соединение с базой данных.
     *
     * @param array $config Конфигурация подключения.
     * @return void
     */
    public function connect(array $config): void;

    /**
     * Проверить активность соединения.
     *
     * @return bool True, если соединение активно.
     */
    public function isConnected(): bool;

    public function getList(): TableManager;
    /**
     * Выполнить SQL-запрос SELECT.
     *
     * @param string $sql SQL-запрос.
     * @param array $params Параметры для подготовленного запроса.
     * @return array Результат выполнения запроса.
     */
    public function query(string $sql, array $params = []): array;

    /**
     * Выполнить SQL-запрос INSERT, UPDATE, DELETE.
     *
     * @param string $sql SQL-запрос.
     * @param array $params Параметры для подготовленного запроса.
     * @return bool Успешность выполнения запроса.
     */
    public function execute(string $sql, array $params = []): bool;

    /**
     * Закрыть соединение с базой данных.
     *
     * @return void
     */
    public function close(): void;

    public function getNameRepository(): string;
}

<?php

namespace MiniCore\Tests\Database;

use MiniCore\Database\DataAction;
use PHPUnit\Framework\TestCase;

/**
 * Class DataActionTest
 *
 * Unit tests for the DataAction class.
 */
class DataActionTest extends TestCase
{
    /**
     * Проверка добавления и получения колонок.
     */
    public function testAddAndGetColumns(): void
    {
        $dataAction = new DataAction();

        $dataAction->addColumn('username');
        $dataAction->addColumn('email');

        $expectedColumns = ['username', 'email'];

        $this->assertEquals($expectedColumns, $dataAction->getColumns(), 'Колонки были добавлены некорректно.');
    }

    /**
     * Проверка добавления и получения условия WHERE.
     */
    public function testAddAndGetWhereProperty(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);

        $expectedProperties = [
            ['type' => 'WHERE', 'condition' => 'id = :id'],
        ];

        $this->assertEquals($expectedProperties, $dataAction->getProperties(), 'Условие WHERE было добавлено некорректно.');
        $this->assertEquals(['id' => 1], $dataAction->getParameters(), 'Параметры WHERE были добавлены некорректно.');
    }

    /**
     * Проверка добавления нескольких условий (WHERE, ORDER BY).
     */
    public function testAddMultipleProperties(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'status = :status', ['status' => 'active']);
        $dataAction->addProperty('ORDER BY', 'created_at DESC');

        $expectedProperties = [
            ['type' => 'WHERE', 'condition' => 'status = :status'],
            ['type' => 'ORDER BY', 'condition' => 'created_at DESC'],
        ];

        $expectedParameters = ['status' => 'active'];

        $this->assertEquals($expectedProperties, $dataAction->getProperties(), 'Условия были добавлены некорректно.');
        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Параметры были добавлены некорректно.');
    }

    /**
     * Проверка добавления параметров к запросу.
     */
    public function testAddParameters(): void
    {
        $dataAction = new DataAction();

        $dataAction->addParameters(['id' => 1]);
        $dataAction->addParameters(['status' => 'active']);

        $expectedParameters = [
            'id' => 1,
            'status' => 'active',
        ];

        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Параметры были добавлены некорректно.');
    }

    /**
     * Проверка получения конкретного свойства по типу.
     */
    public function testGetSpecificProperty(): void
    {
        $dataAction = new DataAction();

        $dataAction->addProperty('WHERE', 'id = :id', ['id' => 1]);
        $dataAction->addProperty('ORDER BY', 'created_at DESC');

        $properties = $dataAction->getProperties();

        $this->assertEquals('WHERE', $properties[0]['type'], 'Тип свойства WHERE не совпадает.');
        $this->assertEquals('id = :id', $properties[0]['condition'], 'Условие WHERE не совпадает.');

        $this->assertEquals('ORDER BY', $properties[1]['type'], 'Тип свойства ORDER BY не совпадает.');
        $this->assertEquals('created_at DESC', $properties[1]['condition'], 'Условие ORDER BY не совпадает.');
    }

    /**
     * Проверка добавления одинаковых параметров (перезапись).
     */
    public function testAddDuplicateParameters(): void
    {
        $dataAction = new DataAction();

        $dataAction->addParameters(['id' => 1]);
        $dataAction->addParameters(['id' => 2]);  // Перезапишет значение

        $expectedParameters = ['id' => 2];

        $this->assertEquals($expectedParameters, $dataAction->getParameters(), 'Дублирующий параметр не был перезаписан.');
    }
}

<?php

namespace MiniCore\Tests\UI;

use PHPUnit\Framework\TestCase;
use MiniCore\UI\AlertManager;
use MiniCore\UI\AlertType;

/**
 * Unit tests for the AlertManager class.
 *
 * This test suite verifies the correct behavior of the AlertManager class,
 * ensuring that alerts are added, managed, and rendered properly.
 *
 * Covered functionality:
 * - Adding single and multiple alerts with various types.
 * - Rendering alerts into HTML format.
 * - Clearing alerts from the storage.
 * - Handling the rendering of empty alerts.
 */
class AlertManagerTest extends TestCase
{
    /**
     * Resets the alert list before each test to ensure a clean state.
     */
    protected function setUp(): void
    {
        AlertManager::clear();
    }

    /**
     * Tests adding a single alert and verifying its storage.
     */
    public function testAddSingleAlert(): void
    {
        AlertManager::addAlert(AlertType::SUCCESS, 'Operation was successful.');

        $alerts = AlertManager::getAlerts();

        $this->assertCount(1, $alerts, 'Expected one alert to be added.');
        $this->assertEquals(AlertType::SUCCESS, $alerts[0]['type'], 'Alert type should be SUCCESS.');
        $this->assertEquals('Operation was successful.', $alerts[0]['message'], 'Alert message should match.');
    }

    /**
     * Tests adding multiple alerts and verifying their order and content.
     */
    public function testAddMultipleAlerts(): void
    {
        AlertManager::addAlert(AlertType::SUCCESS, 'First success message.');
        AlertManager::addAlert(AlertType::ERROR, 'An error occurred.');
        AlertManager::addAlert(AlertType::INFO, 'This is an informational message.');

        $alerts = AlertManager::getAlerts();

        $this->assertCount(3, $alerts, 'Expected three alerts to be added.');

        $this->assertEquals(AlertType::SUCCESS, $alerts[0]['type']);
        $this->assertEquals('First success message.', $alerts[0]['message']);

        $this->assertEquals(AlertType::ERROR, $alerts[1]['type']);
        $this->assertEquals('An error occurred.', $alerts[1]['message']);

        $this->assertEquals(AlertType::INFO, $alerts[2]['type']);
        $this->assertEquals('This is an informational message.', $alerts[2]['message']);
    }

    /**
     * Tests rendering of alerts into HTML format.
     */
    public function testRenderAlerts(): void
    {
        AlertManager::addAlert(AlertType::SUCCESS, 'Saved successfully.');
        AlertManager::addAlert(AlertType::WARNING, 'This is a warning.');

        $expectedHtml = '<div class="alerts-container">'
            . '<div class="alert success">Saved successfully.</div>'
            . '<div class="alert warning">This is a warning.</div>'
            . '</div>';

        $this->assertEquals($expectedHtml, AlertManager::render(), 'Rendered HTML does not match expected output.');
    }

    /**
     * Tests clearing all alerts from the manager.
     */
    public function testClearAlerts(): void
    {
        AlertManager::addAlert(AlertType::ERROR, 'An error occurred.');
        $this->assertNotEmpty(AlertManager::getAlerts(), 'Alerts should not be empty after adding.');

        AlertManager::clear();

        $this->assertEmpty(AlertManager::getAlerts(), 'Alerts should be empty after clearing.');
    }

    /**
     * Tests rendering with no alerts, which should return an empty string.
     */
    public function testRenderEmptyAlerts(): void
    {
        $this->assertEquals('', AlertManager::render(), 'Rendering empty alerts should return an empty string.');
    }
}

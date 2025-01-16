<?php

namespace MiniCore\Tests\UI;

use PHPUnit\Framework\TestCase;
use MiniCore\UI\AlertManager;
use MiniCore\UI\AlertType;

/**
 * Class AlertManagerTest
 *
 * Tests for the AlertManager class.
 */
class AlertManagerTest extends TestCase
{
    /**
     * Reset alerts before each test.
     */
    protected function setUp(): void
    {
        AlertManager::clear();
    }

    /**
     * Test adding a single alert.
     */
    public function testAddSingleAlert(): void
    {
        AlertManager::addAlert(AlertType::SUCCESS, 'Operation was successful.');

        $alerts = AlertManager::getAlerts();

        $this->assertCount(1, $alerts, 'Expected one alert to be added.');
        $this->assertEquals(AlertType::SUCCESS, $alerts[0]['type']);
        $this->assertEquals('Operation was successful.', $alerts[0]['message']);
    }

    /**
     * Test adding multiple alerts.
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
     * Test rendering of alerts as HTML.
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
     * Test clearing of alerts.
     */
    public function testClearAlerts(): void
    {
        AlertManager::addAlert(AlertType::ERROR, 'An error occurred.');
        $this->assertNotEmpty(AlertManager::getAlerts(), 'Alerts should not be empty after adding.');

        AlertManager::clear();

        $this->assertEmpty(AlertManager::getAlerts(), 'Alerts should be empty after clearing.');
    }

    /**
     * Test rendering with no alerts (should return empty string).
     */
    public function testRenderEmptyAlerts(): void
    {
        $this->assertEquals('', AlertManager::render(), 'Rendering empty alerts should return an empty string.');
    }
}

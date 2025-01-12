<?php

namespace MiniCore\UI;

/**
 * Class AlertManager
 *
 * Manages user interface alerts by allowing the addition, retrieval, rendering, and clearing of alert messages.
 * Alerts are typically used to display notifications, warnings, errors, or success messages on the UI.
 *
 * @package MiniCore\UI
 *
 * @example
 * // Adding and rendering alerts:
 * AlertManager::addAlert(AlertType::Success, 'Your data has been saved!');
 * AlertManager::addAlert(AlertType::Error, 'Failed to save data.');
 * echo AlertManager::render();
 */
class AlertManager
{
    /**
     * Stores all added alerts.
     *
     * @var array
     */
    private static array $alerts = [];

    /**
     * Add a new alert to the alert queue.
     *
     * @param AlertType $type The type of the alert (e.g., success, error, warning).
     * @param string $message The message to display in the alert.
     *
     * @example
     * AlertManager::addAlert(AlertType::Warning, 'This is a warning message.');
     */
    public static function addAlert(AlertType $type, string $message): void
    {
        self::$alerts[] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Retrieve all added alerts.
     *
     * @return array The list of current alerts.
     *
     * @example
     * $alerts = AlertManager::getAlerts();
     * foreach ($alerts as $alert) {
     *     echo $alert['type']->value . ': ' . $alert['message'] . PHP_EOL;
     * }
     */
    public static function getAlerts(): array
    {
        return self::$alerts;
    }

    /**
     * Render all alerts as HTML for display on the page.
     *
     * @return string The rendered HTML of all alerts.
     *
     * @example
     * echo AlertManager::render();
     */
    public static function render(): string
    {
        if (empty(self::$alerts)) {
            return ''; // No alerts to render
        }

        $html = '<div class="alerts-container">';

        foreach (self::$alerts as $alert) {
            $html .= sprintf(
                '<div class="alert %s">%s</div>',
                htmlspecialchars($alert['type']->value),
                htmlspecialchars($alert['message'])
            );
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Clear all stored alerts.
     *
     * @example
     * AlertManager::clear();
     */
    public static function clear(): void
    {
        self::$alerts = [];
    }
}

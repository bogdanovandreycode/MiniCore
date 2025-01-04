<?php

namespace Vendor\Undermarket\Core\UI;

class AlertManager
{
    private static array $alerts = [];

    public static function addAlert(AlertType $type, string $message): void
    {
        self::$alerts[] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function getAlerts(): array
    {
        return self::$alerts;
    }

    public static function render(): string
    {
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

    public static function clear(): void
    {
        self::$alerts = [];
    }
}

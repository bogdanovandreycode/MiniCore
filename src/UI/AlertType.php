<?php

namespace MiniCore\UI;

/**
 * Enum AlertType
 *
 * Defines the types of alerts that can be used within the application.
 * Each type represents a specific category of user notifications for better UX.
 *
 * @package MiniCore\UI
 *
 * Available types:
 * - SUCCESS: Used to indicate successful operations.
 * - ERROR: Used to indicate errors or failures.
 * - WARNING: Used to warn users about potential issues.
 * - INFO: Used to provide general informational messages.
 *
 * @example
 * // Adding a success alert
 * AlertManager::addAlert(AlertType::SUCCESS, 'Data saved successfully!');
 *
 * // Adding an error alert
 * AlertManager::addAlert(AlertType::ERROR, 'Failed to save data.');
 */
enum AlertType: string
{
    /**
     * Success alert for confirming positive actions.
     *
     * Example: "Your profile has been updated successfully."
     */
    case SUCCESS = 'success';

    /**
     * Error alert for reporting failures or critical issues.
     *
     * Example: "An error occurred while processing your request."
     */
    case ERROR = 'error';

    /**
     * Warning alert for cautioning users about potential risks.
     *
     * Example: "Your subscription is about to expire."
     */
    case WARNING = 'warning';

    /**
     * Informational alert for general updates or neutral messages.
     *
     * Example: "New features have been added in the latest update."
     */
    case INFO = 'info';
}

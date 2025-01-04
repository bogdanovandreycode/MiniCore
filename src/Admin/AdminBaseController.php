<?php

namespace Vendor\Undermarket\Core\Admin;

abstract class AdminBaseController
{
    /**
     * Check if the user has access to the admin area.
     *
     * @return bool True if the user has access, false otherwise.
     */
    protected function checkAccess(): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    /**
     * Render an admin page.
     *
     * @param string $view The name of the view file.
     * @param array $data Data to pass to the view.
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        if (!$this->checkAccess()) {
            $this->redirect('/admin/login');
            return;
        }

        extract($data);
        $viewPath = __DIR__ . "/../../Views/admin/{$view}.php";

        if (!file_exists($viewPath)) {
            die("View file '{$view}' not found.");
        }

        include $viewPath;
    }

    /**
     * Redirect to another page.
     *
     * @param string $url The URL to redirect to.
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Display an error message in the admin panel.
     *
     * @param string $message The error message to display.
     * @return void
     */
    protected function showError(string $message): void
    {
        echo "<div class='admin-error'>Error: " . htmlspecialchars($message) . "</div>";
    }
}

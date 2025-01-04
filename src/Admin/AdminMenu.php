<?php

namespace Vendor\Undermarket\Core\Admin;

class AdminMenu
{
    private array $menuItems = [];

    /**
     * Add a menu item to the admin panel.
     *
     * @param string $title The display title of the menu item.
     * @param string $url The URL the menu item links to.
     * @param string|null $icon (Optional) The icon class for the menu item (e.g., FontAwesome).
     * @return $this
     */
    public function addMenuItem(string $title, string $url, ?string $icon = null): self
    {
        $this->menuItems[] = [
            'title' => $title,
            'url' => $url,
            'icon' => $icon,
        ];

        return $this;
    }

    /**
     * Remove a menu item by its title.
     *
     * @param string $title The title of the menu item to remove.
     * @return $this
     */
    public function removeMenuItem(string $title): self
    {
        $this->menuItems = array_filter(
            $this->menuItems,
            fn($item) => $item['title'] !== $title
        );

        return $this;
    }

    /**
     * Get all registered menu items.
     *
     * @return array The list of menu items.
     */
    public function getMenuItems(): array
    {
        return $this->menuItems;
    }

    /**
     * Render the admin menu as an HTML list.
     *
     * @return string The rendered HTML menu.
     */
    public function render(): string
    {
        $html = '<ul class="admin-menu">';

        foreach ($this->menuItems as $item) {
            $iconHtml = $item['icon'] ? sprintf('<i class="%s"></i> ', htmlspecialchars($item['icon'])) : '';
            $html .= sprintf(
                '<li><a href="%s">%s%s</a></li>',
                htmlspecialchars($item['url']),
                $iconHtml,
                htmlspecialchars($item['title'])
            );
        }

        $html .= '</ul>';
        return $html;
    }
}

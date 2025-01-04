<?php

namespace MinoCore;

class Setup
{
    public static function initialize(): void
    {
        // Определяем структуру директорий и файлов
        $directories = [
            'Config',
            'Migrations',
            'Models',
            'Views/admin',
            'Views/components',
            'Views/layout',
            'Views/pages',
            'public',
        ];

        $files = [
            'Config/example-config.yml' => "# Example configuration\nkey: value\n",
            'public/index.php' => "<?php\n\nrequire_once __DIR__ . '/../vendor/autoload.php';\n\n\\MinoCore\\Boot::initialize();\n"
        ];

        // Создаём папки, если они не существуют
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "Created directory: {$dir}\n";
            }
        }

        // Создаём файлы, если они не существуют
        foreach ($files as $filePath => $content) {
            if (!file_exists($filePath)) {
                file_put_contents($filePath, $content);
                echo "Created file: {$filePath}\n";
            }
        }
    }
}

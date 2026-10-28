<?php
namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Extract data for view
        extract($data);

        // Flash messages
        $flashes = Session::getFlashes();

        // Current authenticated user
        $currentUser = Session::get('user');

        // Path to view file
        $file = __DIR__ . '/../../views/' . ltrim($viewPath, '/') . '.php';

        if (!file_exists($file)) {
            throw new \Exception("View template [{$viewPath}] not found at {$file}");
        }

        // Buffer the view content
        ob_start();
        include $file;
        $content = ob_get_clean();

        // If layout requested, wrap with layout
        if ($layout !== null) {
            $layoutFile = __DIR__ . '/../../views/' . ltrim($layout, '/') . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                include $layoutFile;
                $content = ob_get_clean();
            }
        }

        echo $content;
        exit;
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

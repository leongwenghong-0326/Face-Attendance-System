<?php
namespace App\Core;

class App
{
    private static Router $router;

    public static function run(): void
    {
        // 1. Load Environment
        $root = dirname(__DIR__, 2);
        Env::load($root . '/.env');

        // 2. Set timezone
        $timezone = Env::get('APP_TIMEZONE', 'Asia/Kuala_Lumpur');
        date_default_timezone_set($timezone);

        // 3. Error Handling
        $debug = Env::get('APP_DEBUG', false);
        if ($debug) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        // 4. Start Session
        Session::start();

        // 5. Initialize Router and Load Routes
        self::$router = new Router();
        require $root . '/app/routes.php';

        // 6. Dispatch Request
        $request = new Request();
        self::$router->dispatch($request);
    }

    public static function router(): Router
    {
        return self::$router;
    }
}

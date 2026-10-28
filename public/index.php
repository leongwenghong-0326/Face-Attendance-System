<?php
/**
 * Web-Based Facial Recognition Student Attendance Management System
 * Front Controller Entry Point
 */

// Strict error reporting during development
error_reporting(E_ALL);

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load global helpers
require_once dirname(__DIR__) . '/app/Helpers/helpers.php';

// Bootstrap and run application
App\Core\App::run();

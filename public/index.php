<?php

if (!defined('APP_ENV')) {
    define('APP_ENV', empty($_SERVER['APP_ENV']) ? 'production' : $_SERVER['APP_ENV']);
}

// Display all errors when APP_ENV is development.
if (APP_ENV == 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Laminas\Mvc\Application::init(require 'config/application.config.php')->run();

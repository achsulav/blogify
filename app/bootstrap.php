<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Custom Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load helper functions
require_once BASE_PATH . '/app/helpers.php';

<?php

date_default_timezone_set('UTC');

// A simple autoloader for the tests
spl_autoload_register(function ($class) {
    if (substr($class, 0, 12) == 'Jejik\\Tests\\') {
        require_once __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    } elseif (substr($class, 0, 6) == 'Jejik\\') {
        require_once __DIR__ . '/../lib/' . str_replace('\\', '/', $class) . '.php';
    }
});

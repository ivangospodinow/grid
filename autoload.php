<?php
require_once 'vendor/autoload.php';

spl_autoload_register(function ($name) {
    if (substr($name, 0, 4) === 'Grid') {
        require_once __DIR__ . '/src/' . $name . '.php';
    }
});
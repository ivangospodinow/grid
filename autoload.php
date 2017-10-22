<?php
spl_autoload_register(function ($name) {
    if (substr($name, 0, 4) === 'Grid') {
        $file = __DIR__ . '/src/' .str_replace('\\', '/', $name) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
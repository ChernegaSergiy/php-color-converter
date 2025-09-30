<?php

require __DIR__ . '/vendor/autoload.php';

use ChernegaSergiy\ColorConverter\App\ColorConverterApp;
use ChernegaSergiy\ColorConverter\UI\Terminal;
use Exception;

if (php_sapi_name() === 'cli') {
    try {
        Terminal::enterAlternativeScreen();
        $app = new ColorConverterApp();
        $app->run();
    } catch (Exception $e) {
        Terminal::disableRawMode();
        Terminal::showCursor();
        Terminal::exitAlternativeScreen();
        echo "\n\033[1;31mКритична помилка:\033[0m " . $e->getMessage() . "\n\n";
        exit(1);
    }
} else {
    echo "Цей скрипт призначений тільки для використання в CLI.\n";
}

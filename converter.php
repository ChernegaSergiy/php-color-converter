<?php

declare(strict_types=1);

require_once __DIR__ . '/src/ColorModel.php';
require_once __DIR__ . '/src/RgbColor.php';
require_once __DIR__ . '/src/CmykColor.php';
require_once __DIR__ . '/src/HsvColor.php';
require_once __DIR__ . '/src/HslColor.php';
require_once __DIR__ . '/src/LabColor.php';
require_once __DIR__ . '/src/YcbcrColor.php';
require_once __DIR__ . '/src/ConversionRecord.php';
require_once __DIR__ . '/src/ConversionHistory.php';
require_once __DIR__ . '/src/Terminal.php';
require_once __DIR__ . '/src/InteractiveMenu.php';
require_once __DIR__ . '/src/InteractiveInput.php';
require_once __DIR__ . '/src/SliderInput.php';
require_once __DIR__ . '/src/ColorPreview.php';
require_once __DIR__ . '/src/ColorConverterApp.php';

use ChernegaSergiy\ColorConverter\ColorConverterApp;
use ChernegaSergiy\ColorConverter\Terminal;
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

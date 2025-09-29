<?php

// Abstract base class for color models
abstract class ColorModel
{
    abstract public function toRgb(): RgbColor;
    abstract public function toString(): string;
    abstract public function toArray(): array;
}

// RGB Color class
class RgbColor extends ColorModel
{
    private int $r;
    private int $g;
    private int $b;

    public function __construct(int $r, int $g, int $b)
    {
        $this->validate($r, $g, $b);
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    private function validate(int $r, int $g, int $b): void
    {
        if ($r < 0 || $r > 255 || $g < 0 || $g > 255 || $b < 0 || $b > 255) {
            throw new InvalidArgumentException("RGB values must be between 0 and 255");
        }
    }

    public function getR(): int { return $this->r; }
    public function getG(): int { return $this->g; }
    public function getB(): int { return $this->b; }

    public function toRgb(): RgbColor
    {
        return $this;
    }

    public function toCmyk(): CmykColor
    {
        $r_norm = $this->r / 255;
        $g_norm = $this->g / 255;
        $b_norm = $this->b / 255;

        $k = 1 - max($r_norm, $g_norm, $b_norm);

        if ($k == 1) {
            return new CmykColor(0, 0, 0, 100);
        }

        $c = ((1 - $r_norm - $k) / (1 - $k)) * 100;
        $m = ((1 - $g_norm - $k) / (1 - $k)) * 100;
        $y = ((1 - $b_norm - $k) / (1 - $k)) * 100;

        return new CmykColor($c, $m, $y, $k * 100);
    }

    public function toHsv(): HsvColor
    {
        $r_norm = $this->r / 255.0;
        $g_norm = $this->g / 255.0;
        $b_norm = $this->b / 255.0;

        $c_max = max($r_norm, $g_norm, $b_norm);
        $c_min = min($r_norm, $g_norm, $b_norm);
        $delta = $c_max - $c_min;

        $h = 0.0;
        $s = 0.0;
        $v = $c_max * 100;

        if ($c_max == 0.0) {
            return new HsvColor(0.0, 0.0, 0.0);
        }

        $s = ($delta / $c_max) * 100;

        if ($delta != 0.0) {
            if ($c_max == $r_norm) {
                $h_prime = ($g_norm - $b_norm) / $delta;
            } elseif ($c_max == $g_norm) {
                $h_prime = ($b_norm - $r_norm) / $delta + 2.0;
            } else {
                $h_prime = ($r_norm - $g_norm) / $delta + 4.0;
            }

            $h = $h_prime * 60.0;
            if ($h < 0.0) {
                $h += 360.0;
            }
        }

        return new HsvColor($h, $s, $v);
    }

    public function toString(): string
    {
        return sprintf("RGB(%d, %d, %d)", $this->r, $this->g, $this->b);
    }

    public function toArray(): array
    {
        return ['r' => $this->r, 'g' => $this->g, 'b' => $this->b];
    }

    public function getHexColor(): string
    {
        return sprintf("#%02x%02x%02x", $this->r, $this->g, $this->b);
    }
}

// CMYK Color class
class CmykColor extends ColorModel
{
    private float $c;
    private float $m;
    private float $y;
    private float $k;

    public function __construct(float $c, float $m, float $y, float $k)
    {
        $this->validate($c, $m, $y, $k);
        $this->c = $c;
        $this->m = $m;
        $this->y = $y;
        $this->k = $k;
    }

    private function validate(float $c, float $m, float $y, float $k): void
    {
        if ($c < 0 || $c > 100 || $m < 0 || $m > 100 ||
            $y < 0 || $y > 100 || $k < 0 || $k > 100) {
            throw new InvalidArgumentException("CMYK values must be between 0 and 100");
        }
    }

    public function getC(): float { return $this->c; }
    public function getM(): float { return $this->m; }
    public function getY(): float { return $this->y; }
    public function getK(): float { return $this->k; }

    public function toRgb(): RgbColor
    {
        $c = $this->c / 100;
        $m = $this->m / 100;
        $y = $this->y / 100;
        $k = $this->k / 100;

        $r = 255 * (1 - $c) * (1 - $k);
        $g = 255 * (1 - $m) * (1 - $k);
        $b = 255 * (1 - $y) * (1 - $k);

        return new RgbColor((int)round($r), (int)round($g), (int)round($b));
    }

    public function toString(): string
    {
        return sprintf("CMYK(%.1f%%, %.1f%%, %.1f%%, %.1f%%)",
                       $this->c, $this->m, $this->y, $this->k);
    }

    public function toArray(): array
    {
        return ['c' => $this->c, 'm' => $this->m, 'y' => $this->y, 'k' => $this->k];
    }
}

// HSV Color class
class HsvColor extends ColorModel
{
    private float $h;
    private float $s;
    private float $v;

    public function __construct(float $h, float $s, float $v)
    {
        $this->validate($h, $s, $v);
        $this->h = $h;
        $this->s = $s;
        $this->v = $v;
    }

    private function validate(float $h, float $s, float $v): void
    {
        if ($h < 0 || $h > 360) {
            throw new InvalidArgumentException("H must be between 0 and 360");
        }
        if ($s < 0 || $s > 100 || $v < 0 || $v > 100) {
            throw new InvalidArgumentException("S and V must be between 0 and 100");
        }
    }

    public function getH(): float { return $this->h; }
    public function getS(): float { return $this->s; }
    public function getV(): float { return $this->v; }

    public function toRgb(): RgbColor
    {
        $s = $this->s / 100;
        $v = $this->v / 100;

        $c = $v * $s;
        $h_prime = $this->h / 60;
        $x = $c * (1 - abs(fmod($h_prime, 2) - 1));

        if ($h_prime >= 0 && $h_prime < 1) {
            list($r1, $g1, $b1) = [$c, $x, 0];
        } elseif ($h_prime >= 1 && $h_prime < 2) {
            list($r1, $g1, $b1) = [$x, $c, 0];
        } elseif ($h_prime >= 2 && $h_prime < 3) {
            list($r1, $g1, $b1) = [0, $c, $x];
        } elseif ($h_prime >= 3 && $h_prime < 4) {
            list($r1, $g1, $b1) = [0, $x, $c];
        } elseif ($h_prime >= 4 && $h_prime < 5) {
            list($r1, $g1, $b1) = [$x, 0, $c];
        } else {
            list($r1, $g1, $b1) = [$c, 0, $x];
        }

        $m = $v - $c;

        $r = ($r1 + $m) * 255;
        $g = ($g1 + $m) * 255;
        $b = ($b1 + $m) * 255;

        return new RgbColor((int)round($r), (int)round($g), (int)round($b));
    }

    public function toString(): string
    {
        return sprintf("HSV(%.1f°, %.1f%%, %.1f%%)", $this->h, $this->s, $this->v);
    }

    public function toArray(): array
    {
        return ['h' => $this->h, 's' => $this->s, 'v' => $this->v];
    }
}

// Conversion history entry
class ConversionRecord
{
    private ColorModel $from;
    private ColorModel $to;
    private DateTime $timestamp;

    public function __construct(ColorModel $from, ColorModel $to)
    {
        $this->from = $from;
        $this->to = $to;
        $this->timestamp = new DateTime();
    }

    public function getFrom(): ColorModel { return $this->from; }
    public function getTo(): ColorModel { return $this->to; }
    public function getTimestamp(): DateTime { return $this->timestamp; }

    public function __toString(): string
    {
        return sprintf("[%s] %s -> %s",
                       $this->timestamp->format('H:i:s'),
                       $this->from->toString(),
                       $this->to->toString());
    }
}

// History manager
class ConversionHistory
{
    private array $records = [];
    private int $maxRecords = 20;

    public function add(ColorModel $from, ColorModel $to): void
    {
        $this->records[] = new ConversionRecord($from, $to);
        if (count($this->records) > $this->maxRecords) {
            array_shift($this->records);
        }
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function clear(): void
    {
        $this->records = [];
    }

    public function isEmpty(): bool
    {
        return empty($this->records);
    }

    public function count(): int
    {
        return count($this->records);
    }
}

// Terminal helper for raw input
class Terminal
{
    private static $sttySettings = null;

    public static function enableRawMode(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return; // Windows не підтримує stty
        }
        self::$sttySettings = shell_exec('stty -g');
        shell_exec('stty -icanon -echo');
    }

    public static function disableRawMode(): void
    {
        if (self::$sttySettings !== null) {
            shell_exec('stty ' . self::$sttySettings);
        }
    }

    public static function readKey(): string
    {
        $key = fread(STDIN, 3);

        // Arrow keys
        if ($key === "\033[A") return 'up';
        if ($key === "\033[B") return 'down';
        if ($key === "\033[C") return 'right';
        if ($key === "\033[D") return 'left';

        // Enter
        if ($key === "\n" || $key === "\r") return 'enter';

        // Backspace
        if ($key === "\x7f" || $key === "\x08") return 'backspace';

        // ESC
        if ($key === "\033") return 'esc';

        return $key;
    }

    public static function clearScreen(): void
    {
        echo "\033[2J\033[H";
    }

    public static function moveCursor(int $row, int $col): void
    {
        echo "\033[{$row};{$col}H";
    }

    public static function hideCursor(): void
    {
        echo "\033[?25l";
    }

    public static function showCursor(): void
    {
        echo "\033[?25h";
    }

    public static function enterAlternativeScreen(): void
    {
        echo "\033[?1049h";
    }

    public static function exitAlternativeScreen(): void
    {
        echo "\033[?1049l";
    }
}

// Interactive menu
class InteractiveMenu
{
    private array $items;
    private int $selected = 0;
    private string $title;

    public function __construct(string $title, array $items)
    {
        $this->title = $title;
        $this->items = $items;
    }

    public function show(): int
    {
        Terminal::enableRawMode();
        Terminal::hideCursor();

        try {
            while (true) {
                $this->render();

                $key = Terminal::readKey();

                if ($key === 'up' && $this->selected > 0) {
                    $this->selected--;
                } elseif ($key === 'down' && $this->selected < count($this->items) - 1) {
                    $this->selected++;
                } elseif ($key === 'enter') {
                    break;
                } elseif ($key === 'esc') {
                    $this->selected = count($this->items) - 1; // Exit option
                    break;
                }
            }
        } finally {
            Terminal::disableRawMode();
            Terminal::showCursor();
        }

        return $this->selected;
    }

    private function render(): void
    {
        Terminal::clearScreen();

        echo "\n  \033[1;36m{$this->title}\033[0m\n\n";

        foreach ($this->items as $index => $item) {
            if ($index === $this->selected) {
                echo "  \033[1;32m▶ {$item}\033[0m\n";
            } else {
                echo "    {$item}\n";
            }
        }

        echo "\n  \033[90mВикористовуйте ↑↓ для навігації, Enter для вибору, ESC для виходу\033[0m\n";
    }
}

// Interactive input with live preview
class InteractiveInput
{
    private string $prompt;
    private int $min;
    private int $max;
    private string $currentValue = '';
    private ?RgbColor $liveColor = null;

    public function __construct(string $prompt, int $min, int $max)
    {
        $this->prompt = $prompt;
        $this->min = $min;
        $this->max = $max;
    }

    public function setLiveColorCallback(callable $callback): void
    {
        $this->liveColorCallback = $callback;
    }

    public function read(): int
    {
        Terminal::enableRawMode();
        Terminal::hideCursor();

        try {
            while (true) {
                $this->render();

                $key = Terminal::readKey();

                if ($key === 'enter' && $this->currentValue !== '') {
                    $value = (int)$this->currentValue;
                    if ($value >= $this->min && $value <= $this->max) {
                        break;
                    }
                } elseif ($key === 'backspace' && strlen($this->currentValue) > 0) {
                    $this->currentValue = substr($this->currentValue, 0, -1);
                } elseif (ctype_digit($key) && strlen($this->currentValue) < 3) {
                    $this->currentValue .= $key;
                }
            }
        } finally {
            Terminal::disableRawMode();
            Terminal::showCursor();
        }

        return (int)$this->currentValue;
    }

    private function render(): void
    {
        Terminal::clearScreen();
        echo "\n  {$this->prompt}\n\n";
        echo "  \033[1;33m{$this->currentValue}_\033[0m\n\n";

        if ($this->currentValue !== '') {
            $value = (int)$this->currentValue;
            if ($value >= $this->min && $value <= $this->max) {
                echo "  \033[32m✓ Валідне значення\033[0m\n";
            } else {
                echo "  \033[31m✗ Значення має бути від {$this->min} до {$this->max}\033[0m\n";
            }
        }

        echo "\n  \033[90mВведіть число та натисніть Enter\033[0m\n";
    }
}

// Slider input for better UX
class SliderInput
{
    private string $label;
    private int $min;
    private int $max;
    private int $value;
    private int $step;

    public function __construct(string $label, int $min, int $max, int $default = null, int $step = 1)
    {
        $this->label = $label;
        $this->min = $min;
        $this->max = $max;
        $this->value = $default ?? $min;
        $this->step = $step;
    }

    public function read(): int
    {
        Terminal::enableRawMode();
        Terminal::hideCursor();

        try {
            while (true) {
                $this->render();

                $key = Terminal::readKey();

                if ($key === 'left' && $this->value > $this->min) {
                    $this->value = max($this->min, $this->value - $this->step);
                } elseif ($key === 'right' && $this->value < $this->max) {
                    $this->value = min($this->max, $this->value + $this->step);
                } elseif ($key === 'enter') {
                    break;
                } elseif (ctype_digit($key)) {
                    // Quick number input
                    $input = new InteractiveInput($this->label, $this->min, $this->max);
                    $this->value = $input->read();
                    break;
                }
            }
        } finally {
            Terminal::disableRawMode();
            Terminal::showCursor();
        }

        return $this->value;
    }

    private function render(): void
    {
        Terminal::clearScreen();

        echo "\n  {$this->label}\n\n";

        // Progress bar
        $barWidth = 50;
        $percentage = ($this->value - $this->min) / ($this->max - $this->min);
        $filled = (int)($barWidth * $percentage);

        echo "  \033[36m";
        echo str_repeat("█", $filled);
        echo "\033[90m";
        echo str_repeat("░", $barWidth - $filled);
        echo "\033[0m\n\n";

        echo "  \033[1;33m{$this->value}\033[0m / {$this->max}\n\n";

        echo "  \033[90m← → для зміни значення, Enter для підтвердження\033[0m\n";
        echo "  \033[90mАбо введіть цифру для швидкого вводу\033[0m\n";
    }
}

// Color preview
class ColorPreview
{
    public static function show(RgbColor $color, bool $compact = false): void
    {
        $r = $color->getR();
        $g = $color->getG();
        $b = $color->getB();

        if ($compact) {
            echo "\033[48;2;{$r};{$g};{$b}m      \033[0m ";
            echo "\033[1m{$color->getHexColor()}\033[0m";
        } else {
            echo "\n  Попередній перегляд:\n\n";
            echo "  \033[48;2;{$r};{$g};{$b}m                    \033[0m\n";
            echo "  \033[48;2;{$r};{$g};{$b}m                    \033[0m\n";
            echo "  \033[48;2;{$r};{$g};{$b}m                    \033[0m\n\n";
            echo "  HEX: \033[1m{$color->getHexColor()}\033[0m\n";
            echo "  RGB: \033[1m{$color->toString()}\033[0m\n";
        }
    }
}

// Main application
class ColorConverterApp
{
    private ConversionHistory $history;

    public function __construct()
    {
        $this->history = new ConversionHistory();
    }

    public function run(): void
    {
        while (true) {
            $menu = new InteractiveMenu(
                "Color Converter Pro",
                [
                    "RGB → CMYK",
                    "CMYK → RGB",
                    "RGB → HSV",
                    "HSV → RGB",
                    "RGB → Всі формати",
                    "Історія конвертацій ({$this->history->count()})",
                    "Вихід"
                ]
            );

            $choice = $menu->show();

            try {
                switch ($choice) {
                    case 0:
                        $this->convertRgbToCmyk();
                        break;
                    case 1:
                        $this->convertCmykToRgb();
                        break;
                    case 2:
                        $this->convertRgbToHsv();
                        break;
                    case 3:
                        $this->convertHsvToRgb();
                        break;
                    case 4:
                        $this->convertAllFromRgb();
                        break;
                    case 5:
                        $this->showHistory();
                        break;
                    case 6:
                        Terminal::clearScreen();
                        Terminal::exitAlternativeScreen();
                        echo "\n  До побачення!\n\n";
                        exit(0);
                }
            } catch (Exception $e) {
                $this->showError($e->getMessage());
            }
        }
    }

    private function convertRgbToCmyk(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → CMYK Конвертація\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $cmyk = $rgb->toCmyk();

        $this->showResult($rgb, $cmyk);
        $this->history->add($rgb, $cmyk);
    }

    private function convertCmykToRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mCMYK → RGB Конвертація\033[0m\n\n";

        $c = (new SliderInput("C (cyan)", 0, 100, 50))->read();
        $m = (new SliderInput("M (magenta)", 0, 100, 50))->read();
        $y = (new SliderInput("Y (yellow)", 0, 100, 50))->read();
        $k = (new SliderInput("K (black)", 0, 100, 50))->read();

        $cmyk = new CmykColor($c, $m, $y, $k);
        $rgb = $cmyk->toRgb();

        $this->showResult($cmyk, $rgb);
        $this->history->add($cmyk, $rgb);
    }

    private function convertRgbToHsv(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → HSV Конвертація\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $hsv = $rgb->toHsv();

        $this->showResult($rgb, $hsv);
        $this->history->add($rgb, $hsv);
    }

    private function convertHsvToRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mHSV → RGB Конвертація\033[0m\n\n";

        $h = (new SliderInput("H (відтінок)", 0, 360, 180))->read();
        $s = (new SliderInput("S (насиченість)", 0, 100, 50))->read();
        $v = (new SliderInput("V (яскравість)", 0, 100, 50))->read();

        $hsv = new HsvColor($h, $s, $v);
        $rgb = $hsv->toRgb();

        $this->showResult($hsv, $rgb);
        $this->history->add($hsv, $rgb);
    }

    private function convertAllFromRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → Всі формати\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $cmyk = $rgb->toCmyk();
        $hsv = $rgb->toHsv();

        Terminal::clearScreen();
        ColorPreview::show($rgb);

        echo "\n  \033[1;32m✓ Результати:\033[0m\n\n";
        echo "  CMYK: \033[1m{$cmyk->toString()}\033[0m\n";
        echo "  HSV:  \033[1m{$hsv->toString()}\033[0m\n\n";

        $this->waitForKey();

        $this->history->add($rgb, $cmyk);
        $this->history->add($rgb, $hsv);
    }

    private function showResult(ColorModel $from, ColorModel $to): void
    {
        Terminal::clearScreen();

        $rgb = $to->toRgb();
        ColorPreview::show($rgb);

        echo "\n  \033[90mВхідні дані:\033[0m\n";
        echo "  {$from->toString()}\n\n";

        echo "  \033[1;32m✓ Результат:\033[0m\n";
        echo "  \033[1m{$to->toString()}\033[0m\n\n";

        $this->waitForKey();
    }

    private function showHistory(): void
    {
        Terminal::clearScreen();

        if ($this->history->isEmpty()) {
            echo "\n  \033[90mІсторія порожня\033[0m\n\n";
            $this->waitForKey();
            return;
        }

        echo "\n  \033[1;36mІсторія конвертацій\033[0m\n\n";

        foreach ($this->history->getRecords() as $i => $record) {
            echo "  " . ($i + 1) . ". " . $record . "\n";

            if (($i + 1) % 10 === 0 && $i < $this->history->count() - 1) {
                echo "\n";
            }
        }

        echo "\n";
        $this->waitForKey();
    }

    private function showError(string $message): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;31m✗ Помилка:\033[0m\n";
        echo "  {$message}\n\n";
        $this->waitForKey();
    }

    private function waitForKey(): void
    {
        echo "  \033[90mНатисніть будь-яку клавішу для продовження...\033[0m\n";
        Terminal::enableRawMode();
        Terminal::hideCursor();
        Terminal::readKey();
        Terminal::disableRawMode();
        Terminal::showCursor();
    }
}

// Entry point
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
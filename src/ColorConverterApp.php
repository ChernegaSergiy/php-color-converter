<?php

declare(strict_types=1);

namespace App\ColorConverter;

use Exception;

/**
 * Main application class for the Color Converter Pro.
 */
class ColorConverterApp
{
    private ConversionHistory $history;

    /**
     * ColorConverterApp constructor.
     */
    public function __construct()
    {
        $this->history = new ConversionHistory();
    }

    /**
     * Runs the main application loop.
     */
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
                    "RGB → HSL",
                    "HSL → RGB",
                    "RGB → Lab",
                    "Lab → RGB",
                    "RGB → YCbCr",
                    "YCbCr → RGB",
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
                        $this->convertRgbToHsl();
                        break;
                    case 5:
                        $this->convertHslToRgb();
                        break;
                    case 6:
                        $this->convertRgbToLab();
                        break;
                    case 7:
                        $this->convertLabToRgb();
                        break;
                    case 8:
                        $this->convertRgbToYcbcr();
                        break;
                    case 9:
                        $this->convertYcbcrToRgb();
                        break;
                    case 10:
                        $this->convertAllFromRgb();
                        break;
                    case 11:
                        $this->showHistory();
                        break;
                    case 12:
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

    /**
     * Handles the RGB to CMYK conversion process.
     */
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

    /**
     * Handles the CMYK to RGB conversion process.
     */
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

    /**
     * Handles the RGB to HSV conversion process.
     */
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

    /**
     * Handles the HSV to RGB conversion process.
     */
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

    /**
     * Handles the RGB to HSL conversion process.
     */
    private function convertRgbToHsl(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → HSL Конвертація\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $hsl = $rgb->toHsl();

        $this->showResult($rgb, $hsl);
        $this->history->add($rgb, $hsl);
    }

    /**
     * Handles the HSL to RGB conversion process.
     */
    private function convertHslToRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mHSL → RGB Конвертація\033[0m\n\n";

        $h = (new SliderInput("H (відтінок)", 0, 360, 180))->read();
        $s = (new SliderInput("S (насиченість)", 0, 100, 50))->read();
        $l = (new SliderInput("L (світлість)", 0, 100, 50))->read();

        $hsl = new HslColor($h, $s, $l);
        $rgb = $hsl->toRgb();

        $this->showResult($hsl, $rgb);
        $this->history->add($hsl, $rgb);
    }

    /**
     * Handles the RGB to Lab conversion process.
     */
    private function convertRgbToLab(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → Lab Конвертація\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $lab = $rgb->toLab();

        $this->showResult($rgb, $lab);
        $this->history->add($rgb, $lab);
    }

    /**
     * Handles the Lab to RGB conversion process.
     */
    private function convertLabToRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mLab → RGB Конвертація\033[0m\n\n";

        $l = (new SliderInput("L (світлість)", 0, 100, 50))->read();
        $a = (new SliderInput("a (зелений-червоний)", -128, 127, 0))->read();
        $b = (new SliderInput("b (синій-жовтий)", -128, 127, 0))->read();

        $lab = new LabColor($l, $a, $b);
        $rgb = $lab->toRgb();

        $this->showResult($lab, $rgb);
        $this->history->add($lab, $rgb);
    }

    /**
     * Handles the RGB to YCbCr conversion process.
     */
    private function convertRgbToYcbcr(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mRGB → YCbCr Конвертація\033[0m\n\n";

        $r = (new SliderInput("R (червоний)", 0, 255, 128))->read();
        $g = (new SliderInput("G (зелений)", 0, 255, 128))->read();
        $b = (new SliderInput("B (синій)", 0, 255, 128))->read();

        $rgb = new RgbColor($r, $g, $b);
        $ycbcr = $rgb->toYcbcr();

        $this->showResult($rgb, $ycbcr);
        $this->history->add($rgb, $ycbcr);
    }

    /**
     * Handles the YCbCr to RGB conversion process.
     */
    private function convertYcbcrToRgb(): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;36mYCbCr → RGB Конвертація\033[0m\n\n";

        $y = (new SliderInput("Y (Luma)", 0, 255, 128))->read();
        $cb = (new SliderInput("Cb (Chroma blue)", 0, 255, 128))->read();
        $cr = (new SliderInput("Cr (Chroma red)", 0, 255, 128))->read();

        $ycbcr = new YcbcrColor($y, $cb, $cr);
        $rgb = $ycbcr->toRgb();

        $this->showResult($ycbcr, $rgb);
        $this->history->add($ycbcr, $rgb);
    }

    /**
     * Handles the RGB to all formats conversion process.
     */
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
        $hsl = $rgb->toHsl();
        $lab = $rgb->toLab();
        $ycbcr = $rgb->toYcbcr();

        Terminal::clearScreen();
        ColorPreview::show($rgb);

        echo "\n  \033[1;32m✓ Результати:\033[0m\n\n";
        echo "  CMYK: \033[1m{$cmyk->toString()}[0m\n";
        echo "  HSV:  \033[1m{$hsv->toString()}[0m\n";
        echo "  HSL:  \033[1m{$hsl->toString()}[0m\n";
        echo "  Lab:  \033[1m{$lab->toString()}[0m\n";
        echo "  YCbCr:  \033[1m{$ycbcr->toString()}[0m\n\n";

        $this->waitForKey();

        $this->history->add($rgb, $cmyk);
        $this->history->add($rgb, $hsv);
        $this->history->add($rgb, $hsl);
        $this->history->add($rgb, $lab);
        $this->history->add($rgb, $ycbcr);
    }

    /**
     * Displays the conversion result.
     * @param ColorModel $from The original color model.
     * @param ColorModel $to The converted color model.
     */
    private function showResult(ColorModel $from, ColorModel $to): void
    {
        Terminal::clearScreen();

        $rgb = $to->toRgb();
        ColorPreview::show($rgb);

        echo "\n  \033[90mВхідні дані:\033[0m\n";
        echo "  {$from->toString()}

";

        echo "  \033[1;32m✓ Результат:\033[0m\n";
        echo "  \033[1m{$to->toString()}\033[0m\n\n";

        $this->waitForKey();
    }

    /**
     * Displays the conversion history.
     */
    private function showHistory(): void
    {
        Terminal::clearScreen();

        if ($this->history->isEmpty()) {
            echo "\n  \033[90mІсторія порожня[0m\n\n";
            $this->waitForKey();
            return;
        }

        echo "\n  \033[1;36mІсторія конвертацій[0m\n\n";

        foreach ($this->history->getRecords() as $i => $record) {
            echo "  " . ($i + 1) . ". " . $record . "\n";

            if (($i + 1) % 10 === 0 && $i < $this->history->count() - 1) {
                echo "\n";
            }
        }

        echo "\n";
        $this->waitForKey();
    }

    /**
     * Displays an error message.
     * @param string $message The error message to display.
     */
    private function showError(string $message): void
    {
        Terminal::clearScreen();
        echo "\n  \033[1;31m✗ Помилка:\033[0m\n";
        echo "  {$message}\n\n";
        $this->waitForKey();
    }

    /**
     * Waits for a key press from the user.
     */
    private function waitForKey(): void
    {
        echo "  [90mНатисніть будь-яку клавішу для продовження...\033[0m\n";
        Terminal::enableRawMode();
        Terminal::hideCursor();
        Terminal::readKey();
        Terminal::disableRawMode();
        Terminal::showCursor();
    }
}
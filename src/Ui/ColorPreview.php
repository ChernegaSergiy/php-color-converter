<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\Ui;

use ChernegaSergiy\ColorConverter\Model\RgbColor;
use ChernegaSergiy\TerminalUi\Component\AbstractComponent;
use ChernegaSergiy\TerminalUi\Terminal\Ansi;
use ChernegaSergiy\TerminalUi\Terminal\Key;

/**
 * Interactive and frame-based color preview component for terminal applications.
 */
class ColorPreview extends AbstractComponent
{
    private RgbColor $color;
    private bool $compact;
    private string $instruction;

    /**
     * @param RgbColor $color       The RGB color to preview.
     * @param string   $label       The label displayed above the preview.
     * @param bool     $compact     If true, displays a single-line compact preview.
     * @param string   $instruction Hint text displayed below the preview.
     */
    public function __construct(
        RgbColor $color,
        string $label = 'Попередній перегляд:',
        bool $compact = false,
        string $instruction = 'Натисніть будь-яку клавішу або Enter для продовження'
    ) {
        parent::__construct($label);
        $this->color = $color;
        $this->compact = $compact;
        $this->instruction = $instruction;
    }

    public function getColor() : RgbColor
    {
        return $this->color;
    }

    public function setColor(RgbColor $color) : self
    {
        $this->color = $color;

        return $this;
    }

    public function handleKey(Key|string $key) : void
    {
        $this->done = true;
    }

    public function getResult() : RgbColor
    {
        return $this->color;
    }

    public function render() : string
    {
        $r = $this->color->getR();
        $g = $this->color->getG();
        $b = $this->color->getB();
        $hex = $this->color->getHexColor();
        $rgbStr = $this->color->toString();

        $bg = Ansi::isEnabled() ? "\033[48;2;{$r};{$g};{$b}m" : '';
        $reset = Ansi::isEnabled() ? "\033[0m" : '';

        if ($this->compact) {
            $block = Ansi::isEnabled() ? "{$bg}      {$reset} " : "[{$hex}] ";

            return $block . Ansi::style(Ansi::BOLD, $hex);
        }

        $lines = [];
        if ('' !== $this->label) {
            $lines[] = Ansi::style(Ansi::BOLD, $this->label);
            $lines[] = '';
        }

        $lines[] = "{$bg}                    {$reset}";
        $lines[] = "{$bg}                    {$reset}";
        $lines[] = "{$bg}                    {$reset}";
        $lines[] = '';
        $lines[] = 'HEX: ' . Ansi::style(Ansi::BOLD, $hex);
        $lines[] = 'RGB: ' . Ansi::style(Ansi::BOLD, $rgbStr);

        if ('' !== $this->instruction) {
            $lines[] = '';
            $lines[] = Ansi::style(Ansi::GRAY, $this->instruction);
        }

        return implode("\n", $lines);
    }

    /**
     * Backwards-compatible static helper to print the color preview to STDOUT.
     */
    public static function show(RgbColor $color, bool $compact = false) : void
    {
        if ($compact) {
            echo (new self($color, compact: true, instruction: ''))->render() . "\n";

            return;
        }

        echo "\n";
        foreach (explode("\n", (new self($color, instruction: ''))->render()) as $line) {
            echo ('' === $line ? '' : '  ' . $line) . "\n";
        }
    }
}

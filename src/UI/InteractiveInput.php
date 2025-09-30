<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\UI;

use ChernegaSergiy\ColorConverter\Model\RgbColor;

/**
 * Provides an interactive input field for terminal applications with live preview.
 */
class InteractiveInput
{
    private string $prompt;
    private int $min;
    private int $max;
    private string $current_value = '';
    private ?RgbColor $live_color = null;
    private $live_color_callback; // Callable for live color preview

    /**
     * InteractiveInput constructor.
     * @param string $prompt The prompt message to display.
     * @param int $min The minimum allowed integer value.
     * @param int $max The maximum allowed integer value.
     */
    public function __construct(string $prompt, int $min, int $max)
    {
        $this->prompt = $prompt;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Sets a callback function for live color preview.
     * The callback will receive the current RgbColor object.
     * @param callable $callback The callback function.
     */
    public function setLiveColorCallback(callable $callback) : void
    {
        $this->live_color_callback = $callback;
    }

    /**
     * Reads an integer input from the user interactively.
     * @return int The validated integer input.
     */
    public function read() : int
    {
        Terminal::enableRawMode();
        Terminal::hideCursor();

        try {
            while (true) {
                $this->render();

                $key = Terminal::readKey();

                if ($key === 'enter' && $this->current_value !== '') {
                    $value = (int)$this->current_value;
                    if ($value >= $this->min && $value <= $this->max) {
                        break;
                    }
                } elseif ($key === 'backspace' && strlen($this->current_value) > 0) {
                    $this->current_value = substr($this->current_value, 0, -1);
                } elseif (ctype_digit($key) && strlen($this->current_value) < 3) {
                    $this->current_value .= $key;
                }
            }
        } finally {
            Terminal::disableRawMode();
            Terminal::showCursor();
        }

        return (int) $this->current_value;
    }

    /**
     * Renders the interactive input field on the terminal.
     */
    private function render() : void
    {
        Terminal::clearScreen();
        echo "\n  {$this->prompt}\n\n";
        echo "  \033[1;33m{$this->current_value}_\033[0m\n\n";

        if ($this->current_value !== '') {
            $value = (int) $this->current_value;
            if ($value >= $this->min && $value <= $this->max) {
                echo "  \033[32m✓ Валідне значення\033[0m\n";
            } else {
                echo "  \033[31m✗ Значення має бути від {$this->min} до {$this->max}\033[0m\n";
            }
        }

        echo "\n  \033[90mВведіть число та натисніть Enter\033[0m\n";
    }
}

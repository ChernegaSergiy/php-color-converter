<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter;

/**
 * Provides an interactive input field for terminal applications with live preview.
 */
class InteractiveInput
{
    private string $prompt;
    private int $min;
    private int $max;
    private string $currentValue = '';
    private ?RgbColor $liveColor = null;
    private $liveColorCallback; // Callable for live color preview

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
    public function setLiveColorCallback(callable $callback): void
    {
        $this->liveColorCallback = $callback;
    }

    /**
     * Reads an integer input from the user interactively.
     * @return int The validated integer input.
     */
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

    /**
     * Renders the interactive input field on the terminal.
     */
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

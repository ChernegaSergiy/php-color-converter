<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\UI;

/**
 * Provides an interactive slider input for terminal applications.
 */
class SliderInput
{
    private string $label;
    private int $min;
    private int $max;
    private int $value;
    private int $step;

    /**
     * SliderInput constructor.
     * @param string $label The label for the slider.
     * @param int $min The minimum value of the slider.
     * @param int $max The maximum value of the slider.
     * @param int|null $default The default value of the slider.
     * @param int $step The step increment/decrement for the slider.
     */
    public function __construct(string $label, int $min, int $max, int $default = null, int $step = 1)
    {
        $this->label = $label;
        $this->min = $min;
        $this->max = $max;
        $this->value = $default ?? $min;
        $this->step = $step;
    }

    /**
     * Displays the slider and allows the user to adjust the value.
     * @return int The selected value.
     */
    public function read() : int
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

    /**
     * Renders the slider on the terminal.
     */
    private function render() : void
    {
        Terminal::clearScreen();

        echo "\n  {$this->label}\n\n";

        // Progress bar
        $bar_width = 50;
        $percentage = ($this->value - $this->min) / ($this->max - $this->min);
        $filled = (int) ($bar_width * $percentage);

        echo "  \033[36m";
        echo str_repeat("█", $filled);
        echo "\033[90m";
        echo str_repeat("░", $bar_width - $filled);
        echo "\033[0m\n\n";

        echo "  \033[1;33m{$this->value}\033[0m / {$this->max}\n\n";

        echo "  \033[90m← → для зміни значення, Enter для підтвердження\033[0m\n";
        echo "  \033[90mАбо введіть цифру для швидкого вводу\033[0m\n";
    }
}

<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\UI;

/**
 * Provides an interactive menu for terminal applications.
 */
class InteractiveMenu
{
    private array $items;
    private int $selected = 0;
    private string $title;

    /**
     * InteractiveMenu constructor.
     * @param string $title The title of the menu.
     * @param array $items An array of menu items (strings).
     */
    public function __construct(string $title, array $items)
    {
        $this->title = $title;
        $this->items = $items;
    }

    /**
     * Displays the interactive menu and returns the index of the selected item.
     * @return int The index of the selected menu item.
     */
    public function show() : int
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

    /**
     * Renders the menu on the terminal.
     */
    private function render() : void
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

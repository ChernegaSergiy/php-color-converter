<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\UI;

use ChernegaSergiy\ColorConverter\Model\RgbColor;

/**
 * Provides functionality to display a color preview in the terminal.
 */
class ColorPreview
{
    /**
     * Displays a color preview in the terminal.
     * @param RgbColor $color The RGB color to preview.
     * @param bool $compact If true, displays a compact preview; otherwise, a detailed preview.
     */
    public static function show(RgbColor $color, bool $compact = false) : void
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

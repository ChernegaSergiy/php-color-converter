<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter;

use InvalidArgumentException;

/**
 * Represents a CMYK color with cyan, magenta, yellow, and key (black) components.
 */
class CmykColor extends ColorModel
{
    private float $c;
    private float $m;
    private float $y;
    private float $k;

    /**
     * CmykColor constructor.
     * @param float $c The cyan component (0-100).
     * @param float $m The magenta component (0-100).
     * @param float $y The yellow component (0-100).
     * @param float $k The key (black) component (0-100).
     * @throws InvalidArgumentException If any CMYK value is out of range.
     */
    public function __construct(float $c, float $m, float $y, float $k)
    {
        $this->validate($c, $m, $y, $k);
        $this->c = $c;
        $this->m = $m;
        $this->y = $y;
        $this->k = $k;
    }

    /**
     * Validates the CMYK color components.
     * @param float $c The cyan component (0-100).
     * @param float $m The magenta component (0-100).
     * @param float $y The yellow component (0-100).
     * @param float $k The key (black) component (0-100).
     * @throws InvalidArgumentException If any CMYK value is out of range.
     */
    private function validate(float $c, float $m, float $y, float $k): void
    {
        if ($c < 0 || $c > 100 || $m < 0 || $m > 100 ||
            $y < 0 || $y > 100 || $k < 0 || $k > 100) {
            throw new InvalidArgumentException("CMYK values must be between 0 and 100");
        }
    }

    /**
     * Get the cyan component.
     * @return float
     */
    public function getC(): float { return $this->c; }

    /**
     * Get the magenta component.
     * @return float
     */
    public function getM(): float { return $this->m; }

    /**
     * Get the yellow component.
     * @return float
     */
    public function getY(): float { return $this->y; }

    /**
     * Get the key (black) component.
     * @return float
     */
    public function getK(): float { return $this->k; }

    /**
     * Converts the CMYK color to RGB. Requires RgbColor to be available.
     * @return RgbColor The color represented in RGB.
     */
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

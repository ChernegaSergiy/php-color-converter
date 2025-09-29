<?php

declare(strict_types=1);

namespace App\ColorConverter;

use InvalidArgumentException;

/**
 * Represents an HSL color with hue, saturation, and lightness components.
 */
class HslColor extends ColorModel
{
    private float $h;
    private float $s;
    private float $l;

    /**
     * HslColor constructor.
     * @param float $h The hue component (0-360).
     * @param float $s The saturation component (0-100).
     * @param float $l The lightness component (0-100).
     * @throws InvalidArgumentException If any HSL value is out of range.
     */
    public function __construct(float $h, float $s, float $l)
    {
        $this->validate($h, $s, $l);
        $this->h = $h;
        $this->s = $s;
        $this->l = $l;
    }

    /**
     * Validates the HSL color components.
     * @param float $h The hue component (0-360).
     * @param float $s The saturation component (0-100).
     * @param float $l The lightness component (0-100).
     * @throws InvalidArgumentException If any HSL value is out of range.
     */
    private function validate(float $h, float $s, float $l): void
    {
        if ($h < 0 || $h > 360 || $s < 0 || $s > 100 || $l < 0 || $l > 100) {
            throw new InvalidArgumentException("HSL values must be between 0 and 360 (H) or 0 and 100 (S, L)");
        }
    }

    /**
     * Get the hue component.
     * @return float
     */
    public function getH(): float { return $this->h; }

    /**
     * Get the saturation component.
     * @return float
     */
    public function getS(): float { return $this->s; }

    /**
     * Get the lightness component.
     * @return float
     */
    public function getL(): float { return $this->l; }

    /**
     * Converts the HSL color to RGB.
     * @return RgbColor The color represented in RGB.
     */
    public function toRgb(): RgbColor
    {
        $h = $this->h / 360;
        $s = $this->s / 100;
        $l = $this->l / 100;

        if ($s == 0) {
            $r = $g = $b = $l; // achromatic
        } else {
            $hue2rgb = function ($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }

        return new RgbColor((int)round($r * 255), (int)round($g * 255), (int)round($b * 255));
    }

    public function toString(): string
    {
        return sprintf("HSL(%.1f°, %.1f%%, %.1f%%)", $this->h, $this->s, $this->l);
    }

    public function toArray(): array
    {
        return ['h' => $this->h, 's' => $this->s, 'l' => $this->l];
    }
}
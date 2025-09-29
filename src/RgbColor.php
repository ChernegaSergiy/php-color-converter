<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter;

use InvalidArgumentException;

/**
 * Represents an RGB color with red, green, and blue components.
 */
class RgbColor extends ColorModel
{
    private int $r;
    private int $g;
    private int $b;

    /**
     * RgbColor constructor.
     * @param int $r The red component (0-255).
     * @param int $g The green component (0-255).
     * @param int $b The blue component (0-255).
     * @throws InvalidArgumentException If any RGB value is out of range.
     */
    public function __construct(int $r, int $g, int $b)
    {
        $this->validate($r, $g, $b);
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    /**
     * Validates the RGB color components.
     * @param int $r The red component (0-255).
     * @param int $g The green component (0-255).
     * @param int $b The blue component (0-255).
     * @throws InvalidArgumentException If any RGB value is out of range.
     */
    private function validate(int $r, int $g, int $b): void
    {
        if ($r < 0 || $r > 255 || $g < 0 || $g > 255 || $b < 0 || $b > 255) {
            throw new InvalidArgumentException("RGB values must be between 0 and 255");
        }
    }

    /**
     * Get the red component.
     * @return int
     */
    public function getR(): int { return $this->r; }

    /**
     * Get the green component.
     * @return int
     */
    public function getG(): int { return $this->g; }

    /**
     * Get the blue component.
     * @return int
     */
    public function getB(): int { return $this->b; }

    public function toRgb(): RgbColor
    {
        return $this;
    }

    /**
     * Converts the RGB color to CMYK.
     * @return CmykColor The color represented in CMYK.
     */
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

    /**
     * Converts the RGB color to HSV.
     * @return HsvColor The color represented in HSV.
     */
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

    /**
     * Converts the RGB color to HSL.
     * @return HslColor The color represented in HSL.
     */
    public function toHsl(): HslColor
    {
        $r = $this->r / 255;
        $g = $this->g / 255;
        $b = $this->b / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = $s = $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0; // achromatic
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $diff + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $diff + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $diff + 4;
                    break;
            }
            $h /= 6;
        }

        return new HslColor(round($h * 360), round($s * 100), round($l * 100));
    }

    /**
     * Converts the RGB color to CIELAB.
     * @return LabColor The color represented in CIELAB.
     */
    public function toLab(): LabColor
    {
        $r = $this->r / 255;
        $g = $this->g / 255;
        $b = $this->b / 255;

        $r = ($r > 0.04045) ? pow(($r + 0.055) / 1.055, 2.4) : $r / 12.92;
        $g = ($g > 0.04045) ? pow(($g + 0.055) / 1.055, 2.4) : $g / 12.92;
        $b = ($b > 0.04045) ? pow(($b + 0.055) / 1.055, 2.4) : $b / 12.92;

        $x = ($r * 0.4124 + $g * 0.3576 + $b * 0.1805) * 100;
        $y = ($r * 0.2126 + $g * 0.7152 + $b * 0.0722) * 100;
        $z = ($r * 0.0193 + $g * 0.1192 + $b * 0.9505) * 100;

        // Reference white D65
        $ref_x = 95.047;
        $ref_y = 100.000;
        $ref_z = 108.883;

        $x = $x / $ref_x;
        $y = $y / $ref_y;
        $z = $z / $ref_z;

        $x = ($x > 0.008856) ? pow($x, 1/3) : (7.787 * $x) + 16/116;
        $y = ($y > 0.008856) ? pow($y, 1/3) : (7.787 * $y) + 16/116;
        $z = ($z > 0.008856) ? pow($z, 1/3) : (7.787 * $z) + 16/116;

        $l = (116 * $y) - 16;
        $a = 500 * ($x - $y);
        $b = 200 * ($y - $z);

        return new LabColor($l, $a, $b);
    }

    /**
     * Converts the RGB color to YCbCr.
     * @return YcbcrColor The color represented in YCbCr.
     */
    public function toYcbcr(): YcbcrColor
    {
        $r = $this->r;
        $g = $this->g;
        $b = $this->b;

        $y = 0.299 * $r + 0.587 * $g + 0.114 * $b;
        $cb = -0.168736 * $r - 0.331264 * $g + 0.5 * $b + 128;
        $cr = 0.5 * $r - 0.418688 * $g - 0.081312 * $b + 128;

        return new YcbcrColor(round($y), round($cb), round($cr));
    }

    public function toString(): string
    {
        return sprintf("RGB(%d, %d, %d)", $this->r, $this->g, $this->b);
    }

    public function toArray(): array
    {
        return ['r' => $this->r, 'g' => $this->g, 'b' => $this->b];
    }

    /**
     * Returns the hexadecimal representation of the RGB color.
     * @return string The hexadecimal color code (e.g., "#RRGGBB").
     */
    public function getHexColor(): string
    {
        return sprintf("#%02x%02x%02x", $this->r, $this->g, $this->b);
    }
}

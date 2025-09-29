<?php

declare(strict_types=1);

namespace App\ColorConverter;

use InvalidArgumentException;

/**
 * Represents a CIELAB color with lightness (L), green-red (a), and blue-yellow (b) components.
 */
class LabColor extends ColorModel
{
    private float $l;
    private float $a;
    private float $b;

    /**
     * LabColor constructor.
     * @param float $l The lightness component (0-100).
     * @param float $a The green-red component (-128 to 127).
     * @param float $b The blue-yellow component (-128 to 127).
     * @throws InvalidArgumentException If any Lab value is out of range.
     */
    public function __construct(float $l, float $a, float $b)
    {
        $this->validate($l, $a, $b);
        $this->l = $l;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * Validates the Lab color components.
     * @param float $l The lightness component (0-100).
     * @param float $a The green-red component (-128 to 127).
     * @param float $b The blue-yellow component (-128 to 127).
     * @throws InvalidArgumentException If any Lab value is out of range.
     */
    private function validate(float $l, float $a, float $b): void
    {
        if ($l < 0 || $l > 100 || $a < -128 || $a > 127 || $b < -128 || $b > 127) {
            throw new InvalidArgumentException("Lab values are out of range.");
        }
    }

    /**
     * Get the lightness component.
     * @return float
     */
    public function getL(): float { return $this->l; }

    /**
     * Get the green-red component.
     * @return float
     */
    public function getA(): float { return $this->a; }

    /**
     * Get the blue-yellow component.
     * @return float
     */
    public function getB(): float { return $this->b; }

    /**
     * Converts the Lab color to RGB. This conversion typically involves an intermediate XYZ conversion.
     * @return RgbColor The color represented in RGB.
     */
    public function toRgb(): RgbColor
    {
        // This is a simplified conversion and might not be perfectly accurate without a full XYZ conversion.
        // For a more accurate conversion, an intermediate XYZ step would be required.

        $y = ($this->l + 16) / 116;
        $x = $this->a / 500 + $y;
        $z = $y - $this->b / 200;

        $x = pow($x, 3) > 0.008856 ? pow($x, 3) : ($x - 16/116) / 7.787;
        $y = pow($y, 3) > 0.008856 ? pow($y, 3) : ($y - 16/116) / 7.787;
        $z = pow($z, 3) > 0.008856 ? pow($z, 3) : ($z - 16/116) / 7.787;

        $x *= 0.95047; // Reference white D65
        $y *= 1.00000;
        $z *= 1.08883;

        $r = $x * 3.2406 + $y * -1.5372 + $z * -0.4986;
        $g = $x * -0.9689 + $y * 1.8758 + $z * 0.0415;
        $b = $x * 0.0557 + $y * -0.2040 + $z * 1.0570;

        $r = round(max(0, min(1, $r)) * 255);
        $g = round(max(0, min(1, $g)) * 255);
        $b = round(max(0, min(1, $b)) * 255);

        return new RgbColor((int)$r, (int)$g, (int)$b);
    }

    public function toString(): string
    {
        return sprintf("Lab(%.1f, %.1f, %.1f)", $this->l, $this->a, $this->b);
    }

    public function toArray(): array
    {
        return ['l' => $this->l, 'a' => $this->a, 'b' => $this->b];
    }
}
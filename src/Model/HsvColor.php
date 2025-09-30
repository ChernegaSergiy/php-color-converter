<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\Model;

use InvalidArgumentException;

/**
 * Represents an HSV color with hue, saturation, and value components.
 */
class HsvColor extends ColorModel
{
    private float $h;
    private float $s;
    private float $v;

    /**
     * HsvColor constructor.
     * @param float $h The hue component (0-360).
     * @param float $s The saturation component (0-100).
     * @param float $v The value component (0-100).
     * @throws InvalidArgumentException If any HSV value is out of range.
     */
    public function __construct(float $h, float $s, float $v)
    {
        $this->validate($h, $s, $v);
        $this->h = $h;
        $this->s = $s;
        $this->v = $v;
    }

    /**
     * Validates the HSV color components.
     * @param float $h The hue component (0-360).
     * @param float $s The saturation component (0-100).
     * @param float $v The value component (0-100).
     * @throws InvalidArgumentException If any HSV value is out of range.
     */
    private function validate(float $h, float $s, float $v) : void
    {
        if ($h < 0 || $h > 360) {
            throw new InvalidArgumentException("H must be between 0 and 360");
        }
        if ($s < 0 || $s > 100 || $v < 0 || $v > 100) {
            throw new InvalidArgumentException("S and V must be between 0 and 100");
        }
    }

    /**
     * Get the hue component.
     * @return float
     */
    public function getH() : float
    {
        return $this->h;
    }

    /**
     * Get the saturation component.
     * @return float
     */
    public function getS() : float
    {
        return $this->s;
    }

    /**
     * Get the value component.
     * @return float
     */
    public function getV() : float
    {
        return $this->v;
    }

    /**
     * Converts the HSV color to RGB. Requires RgbColor to be available.
     * @return RgbColor The color represented in RGB.
     */
    public function toRgb() : RgbColor
    {
        $s = $this->s / 100;
        $v = $this->v / 100;

        $c = $v * $s;
        $h_prime = $this->h / 60;
        $x = $c * (1 - abs(fmod($h_prime, 2) - 1));

        if ($h_prime >= 0 && $h_prime < 1) {
            list($r1, $g1, $b1) = [$c, $x, 0];
        } elseif ($h_prime >= 1 && $h_prime < 2) {
            list($r1, $g1, $b1) = [$x, $c, 0];
        } elseif ($h_prime >= 2 && $h_prime < 3) {
            list($r1, $g1, $b1) = [0, $c, $x];
        } elseif ($h_prime >= 3 && $h_prime < 4) {
            list($r1, $g1, $b1) = [0, $x, $c];
        } elseif ($h_prime >= 4 && $h_prime < 5) {
            list($r1, $g1, $b1) = [$x, 0, $c];
        } else {
            list($r1, $g1, $b1) = [$c, 0, $x];
        }

        $m = $v - $c;

        $r = ($r1 + $m) * 255;
        $g = ($g1 + $m) * 255;
        $b = ($b1 + $m) * 255;

        return new RgbColor((int) round($r), (int) round($g), (int) round($b));
    }

    public function toString() : string
    {
        return sprintf("HSV(%.1f°, %.1f%%, %.1f%%)", $this->h, $this->s, $this->v);
    }

    public function toArray() : array
    {
        return ['h' => $this->h, 's' => $this->s, 'v' => $this->v];
    }
}

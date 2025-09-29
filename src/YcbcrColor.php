<?php

declare(strict_types=1);

namespace App\ColorConverter;

use InvalidArgumentException;

/**
 * Represents a YCbCr color with luma (Y), blue-difference (Cb), and red-difference (Cr) components.
 */
class YcbcrColor extends ColorModel
{
    private float $y;
    private float $cb;
    private float $cr;

    /**
     * YcbcrColor constructor.
     * @param float $y The luma component (0-255).
     * @param float $cb The blue-difference chrominance component (0-255).
     * @param float $cr The red-difference chrominance component (0-255).
     * @throws InvalidArgumentException If any YCbCr value is out of range.
     */
    public function __construct(float $y, float $cb, float $cr)
    {
        $this->validate($y, $cb, $cr);
        $this->y = $y;
        $this->cb = $cb;
        $this->cr = $cr;
    }

    /**
     * Validates the YCbCr color components.
     * @param float $y The luma component (0-255).
     * @param float $cb The blue-difference chrominance component (0-255).
     * @param float $cr The red-difference chrominance component (0-255).
     * @throws InvalidArgumentException If any YCbCr value is out of range.
     */
    private function validate(float $y, float $cb, float $cr): void
    {
        if ($y < 0 || $y > 255 || $cb < 0 || $cb > 255 || $cr < 0 || $cr > 255) {
            throw new InvalidArgumentException("YCbCr values must be between 0 and 255.");
        }
    }

    /**
     * Get the luma component.
     * @return float
     */
    public function getY(): float { return $this->y; }

    /**
     * Get the blue-difference chrominance component.
     * @return float
     */
    public function getCb(): float { return $this->cb; }

    /**
     * Get the red-difference chrominance component.
     * @return float
     */
    public function getCr(): float { return $this->cr; }

    /**
     * Converts the YCbCr color to RGB.
     * @return RgbColor The color represented in RGB.
     */
    public function toRgb(): RgbColor
    {
        $y = $this->y;
        $cb = $this->cb;
        $cr = $this->cr;

        $r = $y + 1.402 * ($cr - 128);
        $g = $y - 0.344136 * ($cb - 128) - 0.714136 * ($cr - 128);
        $b = $y + 1.772 * ($cb - 128);

        $r = round(max(0, min(255, $r)));
        $g = round(max(0, min(255, $g)));
        $b = round(max(0, min(255, $b)));

        return new RgbColor((int)$r, (int)$g, (int)$b);
    }

    public function toString(): string
    {
        return sprintf("YCbCr(%.1f, %.1f, %.1f)", $this->y, $this->cb, $this->cr);
    }

    public function toArray(): array
    {
        return ['y' => $this->y, 'cb' => $this->cb, 'cr' => $this->cr];
    }
}
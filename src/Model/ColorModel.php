<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\Model;

/**
 * Abstract base class for color models.
 */
abstract class ColorModel
{
    /**
     * Converts the color to the RGB color model.
     * @return RgbColor The color represented in RGB.
     */
    abstract public function toRgb() : RgbColor;

    /**
     * Returns a string representation of the color.
     * @return string The string representation of the color.
     */
    abstract public function toString() : string;

    /**
     * Returns an array representation of the color.
     * @return array The array representation of the color.
     */
    abstract public function toArray() : array;
}

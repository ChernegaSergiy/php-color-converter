<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\History;

use ChernegaSergiy\ColorConverter\Model\ColorModel;
use DateTime;

/**
 * Represents a single color conversion record.
 */
class ConversionRecord
{
    private ColorModel $from;
    private ColorModel $to;
    private DateTime $timestamp;

    /**
     * ConversionRecord constructor.
     * @param ColorModel $from The original color model.
     * @param ColorModel $to The converted color model.
     */
    public function __construct(ColorModel $from, ColorModel $to)
    {
        $this->from = $from;
        $this->to = $to;
        $this->timestamp = new DateTime();
    }

    /**
     * Get the original color model.
     * @return ColorModel
     */
    public function getFrom() : ColorModel
    {
        return $this->from;
    }

    /**
     * Get the converted color model.
     * @return ColorModel
     */
    public function getTo() : ColorModel
    {
        return $this->to;
    }

    /**
     * Get the timestamp of the conversion.
     * @return DateTime
     */
    public function getTimestamp() : DateTime
    {
        return $this->timestamp;
    }

    /**
     * Returns a string representation of the conversion record.
     * @return string
     */
    public function __toString() : string
    {
        return sprintf("[%s] %s -> %s",
                       $this->timestamp->format('H:i:s'),
                       $this->from->toString(),
                       $this->to->toString());
    }
}

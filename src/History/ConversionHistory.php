<?php

declare(strict_types=1);

namespace ChernegaSergiy\ColorConverter\History;

use ChernegaSergiy\ColorConverter\Model\ColorModel;

/**
 * Manages a history of color conversions.
 */
class ConversionHistory
{
    /**
     * @var ConversionRecord[] An array of conversion records.
     */
    private array $records = [];
    private int $max_records = 20;

    /**
     * Adds a new conversion record to the history.
     * If the history exceeds max_records, the oldest record is removed.
     * @param ColorModel $from The original color model.
     * @param ColorModel $to The converted color model.
     */
    public function add(ColorModel $from, ColorModel $to) : void
    {
        $this->records[] = new ConversionRecord($from, $to);
        if (count($this->records) > $this->max_records) {
            array_shift($this->records);
        }
    }

    /**
     * Returns all conversion records.
     * @return ConversionRecord[]
     */
    public function getRecords() : array
    {
        return $this->records;
    }

    /**
     * Clears the conversion history.
     */
    public function clear() : void
    {
        $this->records = [];
    }

    /**
     * Checks if the conversion history is empty.
     * @return bool True if the history is empty, false otherwise.
     */
    public function isEmpty() : bool
    {
        return empty($this->records);
    }

    /**
     * Returns the number of records in the history.
     * @return int
     */
    public function count() : int
    {
        return count($this->records);
    }
}

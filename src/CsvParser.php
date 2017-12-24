<?php

/**
 * Converts a CSV file into an associative array.
 *
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class CsvParser
{
    /**
     * The path to CSV file.
     *
     * @var string
     */
    private $file;

    /**
     * Whether this CSV file has a header row.
     *
     * @var boolean
     */
    private $headerRow;

    /**
     * Constructor.
     *
     * @param string  $file      The path to an CSV file
     * @param boolean $headerRow Whether this CSV has a header row
     */
    public function __construct($file, $headerRow = true)
    {
        $this->file = $file;
        $this->headerRow = $headerRow;
    }

    /**
     * Get the path to the CSV file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get whether this CSV has a header row.
     *
     * @return boolean
     */
    public function hasHeaderRow()
    {
        return $this->headerRow;
    }

    /**
     * Parse a CSV file into an array.
     *
     * @return array
     */
    public function getItems()
    {
        $rows = $this->getRows();

        return $this->hasHeaderRow() ? self::itemsWithHeaders($rows) : $rows;
    }

    /**
     * Read the rows of the CSV file.
     *
     * @return array
     */
    private function getRows()
    {
        $rows = array();
        $handle = fopen($this->getFile(), 'r');
        $expectedColumns = null;

        while (!feof($handle)) {
            if ($row = fgetcsv($handle)) {
                $countColumns = count($row);

                if ($expectedColumns && $expectedColumns !== $countColumns) {
                    throw new Exception('Invalid CSV');
                } else {
                    $expectedColumns = $countColumns;
                }

                $rows[] = $row;
            }
        }

        fclose($handle);

        $rows = self::sanitizeRows($rows);

        return $rows;
    }

    /**
     * Arrange an array of parsed rows using a header row.
     *
     * @param  array $rows An array of CSV rows, each one an array.
     * @return array
     */
    private static function itemsWithHeaders(array $rows)
    {
        $items = array();

        $headers = array_map('self::sanitizeKey', array_shift($rows));

        foreach ($rows as $k => $row) {
            foreach ($row as $k2 => $cell) {
                $items[$k][$headers[$k2]] = $cell;
            }
        }

        return $items;
    }

    /**
     * Sanitize the data and remove empty rows.
     *
     * @param  array $rows An array of CSV rows, each one an array.
     * @return array
     */
    private static function sanitizeRows(array $rows)
    {
        $rows = array_values(array_filter($rows));

        foreach ($rows as $k => $row) {
            $rows[$k] = array_map('self::sanitizeCell', $row);

            if (!implode('', $rows[$k])) {
                unset($rows[$k]);
            }
        }

        return $rows;
    }

    /**
     * Sanitize a string into a simple array key.
     *
     * @param  string $key An array key to use for the data
     * @return string
     */
    private static function sanitizeKey($key)
    {
        $key = strtolower($key);
        $key = preg_replace('/[\s-_]+/', '_', $key);
        $key = preg_replace('/[^a-z0-9_]/i', '', $key);
        $key = trim($key, '_');

        return $key;
    }

    /**
     * Sanitize an individual CSV cell.
     *
     * @param  string $cell A CSV cell
     * @return string|null
     */
    private static function sanitizeCell($cell)
    {
        $cell = trim(stripslashes($cell));

        if (strtolower($cell) === 'null') {
            $cell = null;
        }

        return $cell;
    }
}

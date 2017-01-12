<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Responses\Formatters;

/**
 * Defines the padding formatter
 */
class PaddingFormatter
{
    /** @var bool Whether or not to pad after the string */
    private $padAfter = true;
    /** @var string The padding string */
    private $paddingString = ' ';
    /** @var string The end-of-line character */
    private $eolChar = PHP_EOL;

    /**
     * Formats rows of text so that each column is the same width
     *
     * @param array $rows The rows to pad
     * @param callable $callback The callback that returns a formatted row of text
     * @return string A list of formatted rows
     */
    public function format(array $rows, callable $callback) : string
    {
        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        $maxLengths = $this->normalizeColumns($rows);
        $paddingType = $this->padAfter ? STR_PAD_RIGHT : STR_PAD_LEFT;

        // Format the rows
        foreach ($rows as &$row) {
            foreach ($row as $index => &$item) {
                $item = str_pad($item, $maxLengths[$index], $this->paddingString, $paddingType);
            }
        }

        $formattedText = '';

        foreach ($rows as &$row) {
            $formattedText .= $callback($row) . $this->eolChar;
        }

        // Trim the excess separator
        $formattedText = preg_replace('/' . preg_quote($this->eolChar, '/') . '$/', '', $formattedText);

        return $formattedText;
    }

    /**
     * @return string
     */
    public function getEolChar() : string
    {
        return $this->eolChar;
    }

    /**
     * Normalizes the number of columns in each row
     *
     * @param array $rows The rows to equalize
     * @return array The max length of each column
     */
    public function normalizeColumns(array &$rows) : array
    {
        $maxNumColumns = 0;

        // Find the max number of columns that appear in any given row
        foreach ($rows as $row) {
            $maxNumColumns = max($maxNumColumns, count($row));
        }

        $maxLengths = array_pad([], $maxNumColumns, 0);

        // Normalize the number of columns in each row
        foreach ($rows as &$row) {
            $row = array_pad($row, $maxNumColumns, '');
        }

        // Get the length of the longest value in each column
        foreach ($rows as &$row) {
            foreach ($row as $column => &$value) {
                $value = trim($value);
                $maxLengths[$column] = max($maxLengths[$column], mb_strlen($value));
            }
        }

        return $maxLengths;
    }

    /**
     * @param string $eolChar
     */
    public function setEolChar(string $eolChar)
    {
        $this->eolChar = $eolChar;
    }

    /**
     * @param bool $padAfter
     */
    public function setPadAfter(bool $padAfter)
    {
        $this->padAfter = $padAfter;
    }

    /**
     * @param string $paddingString
     */
    public function setPaddingString(string $paddingString)
    {
        $this->paddingString = $paddingString;
    }
}

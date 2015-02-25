<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a table formatter
 */
namespace RDev\Console\Responses\Formatters;

class Table
{
    /** @var Padding The padding formatter */
    private $padding = null;
    /** @var array The list of headers */
    private $headers = [];
    /** @var array The list of rows */
    private $rows = [];

    /**
     * @param Padding $padding The padding formatter
     */
    public function __construct(Padding $padding)
    {
        $this->padding = $padding;
    }

    /**
     * Adds a header
     *
     * @param string $header The header to add
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Adds a row
     *
     * @param array $row The row to add
     */
    public function addRow(array $row)
    {
        $this->rows[] = $row;
    }

    /**
     * Formats the table into a string
     *
     * @param bool $padAfter True if we are going to add padding after the string, otherwise we'll add it before
     * @param string $paddingString The string to pad with
     * @param string $eolChar The string to insert in between each line
     * @param string $verticalBorder The character to use for vertical borders
     * @param string $horizontalBorder The character to use for horizontal borders
     * @param string $intersection The character to use for row/column intersections
     * @return string The formatted table
     */
    public function format(
        $padAfter = true,
        $paddingString = " ",
        $eolChar = PHP_EOL,
        $verticalBorder = "|",
        $horizontalBorder = "-",
        $intersection = "+"
    )
    {
        if(count($this->rows) == 0)
        {
            return "";
        }

        // If there are headers, we want them to be formatted along with the rows
        $lines = count($this->headers) == 0 ? $this->rows : array_merge([$this->headers], $this->rows);
        $maxLengths = $this->padding->getMaxLengths($lines);
        $rowText = explode($eolChar, $this->padding->format($lines, function($line) use ($paddingString, $verticalBorder)
        {
            $innerText = implode($paddingString . $verticalBorder . $paddingString, $line);

            return $verticalBorder . $paddingString . $innerText . $paddingString . $verticalBorder;
        }, $padAfter, " ", $eolChar));

        // Create the borders
        $borders = [];

        foreach($maxLengths as $maxLength)
        {
            $borders[] = str_repeat($horizontalBorder, $maxLength + 2);
        }

        $borderText = $intersection . implode($intersection, $borders) .$intersection;
        $headerText = "";

        if(count($this->headers) > 0)
        {
            $headerText .= array_shift($rowText) . $eolChar;
            $headerText .= $borderText . $eolChar;
        }

        return $borderText . $eolChar . $headerText . implode($eolChar, $rowText) . $eolChar . $borderText;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param array $rows
     */
    public function setRows(array $rows)
    {
        foreach($rows as &$row)
        {
            $row = (array)$row;
        }

        $this->rows = $rows;
    }
}
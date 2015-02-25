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
    /** @var string The padding string */
    private $cellPaddingString = " ";
    /** @var string The character to use for vertical borders */
    private $verticalBorderChar = "|";
    /** @var string The character to use for horizontal borders */
    private $horizontalBorderChar = "-";
    /** @var string The character to use for row/column intersections */
    private $intersectionChar = "+";

    /**
     * @param Padding $padding The padding formatter
     */
    public function __construct(Padding $padding)
    {
        $this->padding = $padding;
    }

    /**
     * Formats the table into a string
     *
     * @param array $rows The list of rows
     * @param array $headers The list of headers
     * @return string The formatted table
     */
    public function format(array $rows, array $headers = [])
    {
        if(count($rows) == 0)
        {
            return "";
        }

        foreach($rows as &$row)
        {
            $row = (array)$row;
        }

        // If there are headers, we want them to be formatted along with the rows
        $headersAndRows = count($headers) == 0 ? $rows : array_merge([$headers], $rows);
        $maxLengths = $this->padding->equalizeLineLengths($headersAndRows);
        $eolChar = $this->padding->getEOLChar();
        $rowText = explode($eolChar, $this->padding->format($headersAndRows, function($line)
        {
            return sprintf(
                "%s%s%s%s%s",
                $this->verticalBorderChar,
                $this->cellPaddingString,
                implode($this->cellPaddingString . $this->verticalBorderChar . $this->cellPaddingString, $line),
                $this->cellPaddingString,
                $this->verticalBorderChar
            );
        }));

        // Create the borders
        $borders = [];

        foreach($maxLengths as $maxLength)
        {
            $borders[] = str_repeat($this->horizontalBorderChar, $maxLength + 2 * mb_strlen($this->cellPaddingString));
        }

        $borderText = $this->intersectionChar . implode($this->intersectionChar, $borders) .$this->intersectionChar;
        $headerText = count($headers) > 0 ? array_shift($rowText) . $eolChar . $borderText . $eolChar : "";

        return $borderText . $eolChar . $headerText . implode($eolChar, $rowText) . $eolChar . $borderText;
    }

    /**
     * @param string $cellPaddingString
     */
    public function setCellPaddingString($cellPaddingString)
    {
        $this->cellPaddingString = $cellPaddingString;
    }

    /**
     * @param string $eolChar
     */
    public function setEOLChar($eolChar)
    {
        $this->padding->setEOLChar($eolChar);
    }

    /**
     * @param string $horizontalBorderChar
     */
    public function setHorizontalBorderChar($horizontalBorderChar)
    {
        $this->horizontalBorderChar = $horizontalBorderChar;
    }

    /**
     * @param string $intersectionChar
     */
    public function setIntersectionChar($intersectionChar)
    {
        $this->intersectionChar = $intersectionChar;
    }

    /**
     * @param boolean $padAfter
     */
    public function setPadAfter($padAfter)
    {
        $this->padding->setPadAfter($padAfter);
    }

    /**
     * @param string $verticalBorderChar
     */
    public function setVerticalBorderChar($verticalBorderChar)
    {
        $this->verticalBorderChar = $verticalBorderChar;
    }
}
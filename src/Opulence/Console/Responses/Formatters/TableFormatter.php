<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Responses\Formatters;

/**
 * Defines a table formatter
 */
class TableFormatter
{
    /** @var PaddingFormatter The padding formatter */
    private $padding;
    /** @var string The padding string */
    private $cellPaddingString = ' ';
    /** @var string The character to use for vertical borders */
    private $verticalBorderChar = '|';
    /** @var string The character to use for horizontal borders */
    private $horizontalBorderChar = '-';
    /** @var string The character to use for row/column intersections */
    private $intersectionChar = '+';

    /**
     * @param PaddingFormatter $padding The padding formatter
     */
    public function __construct(PaddingFormatter $padding)
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
    public function format(array $rows, array $headers = []): string
    {
        if (count($rows) === 0) {
            return '';
        }

        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        unset($row);

        // If there are headers, we want them to be formatted along with the rows
        $headersAndRows = count($headers) === 0 ? $rows : array_merge([$headers], $rows);
        $maxLengths = $this->padding->normalizeColumns($headersAndRows);
        $eolChar = $this->padding->getEolChar();
        $rowText = explode($eolChar, $this->padding->format($headersAndRows, function ($row) {
            return sprintf(
                '%s%s%s%s%s',
                $this->verticalBorderChar,
                $this->cellPaddingString,
                implode($this->cellPaddingString . $this->verticalBorderChar . $this->cellPaddingString, $row),
                $this->cellPaddingString,
                $this->verticalBorderChar
            );
        }));

        // Create the borders
        $borders = [];

        foreach ($maxLengths as $maxLength) {
            $borders[] = str_repeat($this->horizontalBorderChar, $maxLength + 2 * mb_strlen($this->cellPaddingString));
        }

        $borderText = $this->intersectionChar . implode($this->intersectionChar, $borders) . $this->intersectionChar;
        $headerText = count($headers) > 0 ? array_shift($rowText) . $eolChar . $borderText . $eolChar : '';

        return $borderText . $eolChar . $headerText . implode($eolChar, $rowText) . $eolChar . $borderText;
    }

    /**
     * @param string $cellPaddingString
     */
    public function setCellPaddingString(string $cellPaddingString): void
    {
        $this->cellPaddingString = $cellPaddingString;
    }

    /**
     * @param string $eolChar
     */
    public function setEolChar(string $eolChar): void
    {
        $this->padding->setEolChar($eolChar);
    }

    /**
     * @param string $horizontalBorderChar
     */
    public function setHorizontalBorderChar(string $horizontalBorderChar): void
    {
        $this->horizontalBorderChar = $horizontalBorderChar;
    }

    /**
     * @param string $intersectionChar
     */
    public function setIntersectionChar(string $intersectionChar): void
    {
        $this->intersectionChar = $intersectionChar;
    }

    /**
     * @param bool $padAfter
     */
    public function setPadAfter(bool $padAfter): void
    {
        $this->padding->setPadAfter($padAfter);
    }

    /**
     * @param string $verticalBorderChar
     */
    public function setVerticalBorderChar(string $verticalBorderChar): void
    {
        $this->verticalBorderChar = $verticalBorderChar;
    }
}

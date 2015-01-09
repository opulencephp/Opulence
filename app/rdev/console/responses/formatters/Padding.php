<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the padding formatter
 */
namespace RDev\Console\Responses\Formatters;

class Padding
{
    /**
     * Formats lines of text so that the first item in each line has an equal amount of padding around it
     *
     * @param array $lines The list of lines, which can have the following formats:
     *      A string of items to pad,
     *      An array with two entries:  the string to pad and the text that comes after it
     * @param callable $callback The callback that returns a formatted single line of text
     * @param bool $padAfter True if we are going to add padding after the string, otherwise we'll add it before
     * @param string $paddingString The string to pad with
     * @param string $eolChar The string to insert in between each line
     * @return array A list of formatted lines
     * @throws \InvalidArgumentException Thrown if the input lines are not of the correct format
     */
    public function format(array $lines, callable $callback, $padAfter = true, $paddingString = " ", $eolChar = PHP_EOL)
    {
        if(count($lines) > 0 && is_array($lines[0]))
        {
            $formattedLines = $this->formatLineArrays($lines, $padAfter, $paddingString);
        }
        else
        {
            $formattedLines = $this->formatLineStrings($lines, $padAfter, $paddingString);
        }

        $formattedText = "";

        foreach($formattedLines as $formattedLine)
        {
            $formattedText .= call_user_func($callback, $formattedLine) . $eolChar;
        }

        // Trim the excess separator
        $formattedText = preg_replace("/" . preg_quote($eolChar, "/") . "$/", "", $formattedText);

        return $formattedText;
    }

    /**
     * Formats lines of text so that the first item in each line has an equal amount of spacing around it
     *
     * @param array $lines The list of array lines
     * @param bool $addPaddingAfter True if we are going to add padding after the string, otherwise we'll add it before
     * @param string $paddingString The string to pad with
     * @return array The formatted lines
     */
    private function formatLineArrays(array $lines, $addPaddingAfter, $paddingString)
    {
        $maxLength = 0;

        // Get the max length and validate lines
        foreach($lines as &$line)
        {
            if(count($line) != 2)
            {
                throw new \InvalidArgumentException("Each line must be an array with 2 items");
            }

            $line[0] = trim($line[0]);
            $maxLength = max($maxLength, strlen($line[0]));
        }

        // Format the lines
        foreach($lines as &$line)
        {
            $line[0] = str_pad($line[0], $maxLength, $paddingString, $addPaddingAfter ? STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        return $lines;
    }

    /**
     * Formats lines of text so that each line has an equal amount of spacing around it
     *
     * @param array $lines The list of string lines
     * @param bool $addPaddingAfter True if we are going to add padding after the string, otherwise we'll add it before
     * @param string $paddingString The string to pad with
     * @return array The formatted lines
     */
    private function formatLineStrings(array $lines, $addPaddingAfter, $paddingString)
    {
        $maxLength = 0;

        // Get the max length
        foreach($lines as &$line)
        {
            $line = trim($line);
            $maxLength = max($maxLength, strlen($line));
        }

        // Format the lines
        foreach($lines as &$line)
        {
            $line = str_pad($line, $maxLength, $paddingString, $addPaddingAfter ? STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        return $lines;
    }
}
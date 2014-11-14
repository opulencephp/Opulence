<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the compiler for a route
 */
namespace RDev\Routing\Compilers;
use RDev\Routing;

class Compiler implements ICompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Routing\Route &$route)
    {
        $route->setPathRegex($this->convertRawStringToRegex($route, $route->getRawPath()));
        $route->setHostRegex($this->convertRawStringToRegex($route, $route->getRawHost()));
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableMatchingRegex()
    {
        return "/(\{([^\}]+)\})/";
    }

    /**
     * Converts a raw string with variables to a regex
     *
     * @param Routing\Route $route The route whose string we're converting
     * @param string $rawString The raw string to convert
     * @return string The regex
     * @throws Routing\RouteException
     */
    private function convertRawStringToRegex(Routing\Route &$route, $rawString)
    {
        if(empty($rawString))
        {
            return "/^.*$/";
        }

        $regex = $this->quoteStaticText($rawString);
        $routeVariables = [];
        $matches = [];

        preg_match_all($this->getVariableMatchingRegex(), $rawString, $matches, PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $variableName = $match[2];
            $defaultValue = "";
            $isOptional = false;

            // Set the default value
            if(($equalPos = strpos($match[2], "=")) !== false)
            {
                $variableName = substr($match[2], 0, $equalPos);
                $defaultValue = substr($match[2], $equalPos + 1);
            }

            // Check if the variable is marked as optional
            if(strpos($variableName, "?") !== false)
            {
                $isOptional = true;
                $variableName = substr($variableName, 0, -1);
            }

            // Check that the variable name is a valid PHP variable name
            // @link http://php.net/manual/en/language.variables.basics.php
            if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $variableName))
            {
                throw new Routing\RouteException("Invalid variable name \"$variableName\"");
            }

            if(in_array($variableName, $routeVariables))
            {
                throw new Routing\RouteException("Route uses multiple references to \"$variableName\"");
            }

            $routeVariables[] = $variableName;
            $route->setDefaultValue($variableName, $defaultValue);
            $variableRegex = $route->getVariableRegex($variableName);

            if($variableRegex === null)
            {
                // Add a default regex
                $variableRegex = ".+";
            }

            // Insert the regex for this variable back into the regex
            $regex = str_replace(
                sprintf("{%s}", $match[2]),
                // This gives us the ability to name the match the same as the variable name
                sprintf("(?P<%s>%s)%s", $variableName, $variableRegex, $isOptional ? "?" : ""),
                $regex
            );
        }

        return sprintf("/^%s$/", $regex);
    }

    /**
     * Quotes the static text (text not in braces) for use in a regex
     *
     * @param string $string The string to quote
     * @return string The string with the static text quoted
     * @throws Routing\RouteException Thrown if the braces are not nested correctly
     */
    private function quoteStaticText($string)
    {
        $quotedString = "";
        $stringLength = strlen($string);
        $braceDepth = 0;
        $quoteBuffer = "";

        for($charIter = 0;$charIter < $stringLength;$charIter++)
        {
            $char = $string[$charIter];

            if($char == "{")
            {
                // Flush out the quote buffer
                if($braceDepth == 0 && strlen($quoteBuffer) > 0)
                {
                    $quotedString .= preg_quote($quoteBuffer, "/");
                    $quoteBuffer = "";
                }

                $braceDepth++;
            }
            elseif($char == "}")
            {
                $braceDepth--;
            }

            // Make sure that we didn't JUST close all the braces
            if($braceDepth == 0 && $char != "}")
            {
                $quoteBuffer .= $char;
            }
            else
            {
                $quotedString .= $char;
            }
        }

        // Flush out the buffer
        if(strlen($quoteBuffer) > 0)
        {
            $quotedString .= preg_quote($quoteBuffer, "/");
        }

        if($braceDepth != 0)
        {
            $message = "Route has " . ($braceDepth > 0 ? "unclosed" : "unopened") . " braces";

            throw new Routing\RouteException($message);
        }

        return $quotedString;
    }
} 
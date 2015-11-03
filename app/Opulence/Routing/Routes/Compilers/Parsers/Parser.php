<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers\Parsers;

use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines the route parser
 */
class Parser implements IParser
{
    /** @var string The variable matching regex */
    private static $variableMatchingRegex = "#:([\w]+)(?:=([^:\[\]/]+))?#";
    /** @var int The cursor of the currently parsed route */
    private $cursor = 0;
    /** @var array The list of variable names in the currently parsed route */
    private $variableNames = [];

    /**
     * @inheritdoc
     */
    public function parse(Route $route)
    {
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setPathRegex($this->convertRawStringToRegex($parsedRoute, $parsedRoute->getRawPath()));
        $parsedRoute->setHostRegex($this->convertRawStringToRegex($parsedRoute, $parsedRoute->getRawHost()));

        return $parsedRoute;
    }

    /**
     * Converts a raw string with variables to a regex
     *
     * @param ParsedRoute $parsedRoute The route whose string we're converting
     * @param string $rawString The raw string to convert
     * @return string The regex
     * @throws RouteException Thrown if the route variables are not correctly defined
     */
    private function convertRawStringToRegex(ParsedRoute &$parsedRoute, $rawString)
    {
        if (empty($rawString)) {
            return "#^.*$#";
        }

        $this->variableNames = [];
        $regexPieces = [];
        $quotedTextBuffer = "";
        $bracketDepth = 0;

        foreach (explode("/", $rawString) as $segment) {
            $this->cursor = 0;
            $segmentLength = mb_strlen($segment);

            if ($segmentLength == 0) {
                // There was nothing in this segment, so add an empty regex and continue
                $regexPieces[] = "";
                continue;
            }

            $segmentRegex = "";

            while ($this->cursor < $segmentLength) {
                $char = $segment[$this->cursor];

                switch ($char) {
                    case ":":
                        if (!empty($quotedTextBuffer)) {
                            $segmentRegex .= preg_quote($quotedTextBuffer, "#");
                            $quotedTextBuffer = "";
                        }

                        $segmentRegex .= $this->getVarRegex($parsedRoute, mb_substr($segment, $this->cursor));
                        break;
                    case "[":
                        if (!empty($quotedTextBuffer)) {
                            $segmentRegex .= preg_quote($quotedTextBuffer, "#");
                            $quotedTextBuffer = "";
                        }

                        $segmentRegex .= "(?:";
                        $bracketDepth++;
                        $this->cursor++;
                        break;
                    case "]":
                        if (!empty($quotedTextBuffer)) {
                            $segmentRegex .= preg_quote($quotedTextBuffer, "#");
                            $quotedTextBuffer = "";
                        }

                        $segmentRegex .= ")?";
                        $bracketDepth--;
                        $this->cursor++;
                        break;
                    default:
                        $quotedTextBuffer .= $char;
                        $this->cursor++;
                }
            }

            // Finish flushing out the buffer
            if (!empty($quotedTextBuffer)) {
                $segmentRegex .= preg_quote($quotedTextBuffer, "#");
                $quotedTextBuffer = "";
            }

            $regexPieces[] = $segmentRegex;
        }

        if ($bracketDepth != 0) {
            throw new RouteException(
                sprintf("Route has %s brackets", $bracketDepth > 0 ? "unclosed" : "unopened")
            );
        }

        return sprintf("#^%s$#", implode(preg_quote("/", "#"), $regexPieces));
    }

    /**
     * Parses a variable and returns the regex
     *
     * @param ParsedRoute $parsedRoute The route being parsed
     * @param string $segment The segment being parsed
     * @return string The variable regex
     * @throws RouteException Thrown if the variable definition is invalid
     */
    private function getVarRegex(ParsedRoute $parsedRoute, $segment)
    {
        preg_match(self::$variableMatchingRegex, $segment, $matches);
        $variableName = $matches[1];
        $defaultValue = isset($matches[2]) ? $matches[2] : "";

        if (in_array($variableName, $this->variableNames)) {
            throw new RouteException("Route uses multiple references to \"$variableName\"");
        }

        $this->variableNames[] = $variableName;
        $parsedRoute->setDefaultValue($variableName, $defaultValue);
        $variableRegex = $parsedRoute->getVarRegex($variableName);

        if ($variableRegex === null) {
            // Add a default regex
            $variableRegex = "[^\/:]+";
        }

        $this->cursor += mb_strlen($matches[0]);

        return sprintf("(?P<%s>%s)", $variableName, $variableRegex);
    }
}
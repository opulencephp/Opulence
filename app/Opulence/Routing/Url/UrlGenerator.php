<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Url;

use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\RouteCollection;

/**
 * Defines a URL generator
 */
class UrlGenerator
{
    /** @var string The regex used to match variables */
    private static $variableMatchingRegex = "#:([\w]+)(?:=([^:\[\]/]+))?#";
    /** @var string The regex used to remove leftover brackets and variables */
    private static $leftoverBracketsAndVariablesRegex = "#(\[/?:.+\]|\[|\])|(:([\w]+)(?:=([^:\[\]/]+))?)#";
    /** @var RouteCollection The list of routes */
    private $routeCollection = null;

    /**
     * @param RouteCollection $routeCollection The list of routes
     */
    public function __construct(RouteCollection &$routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    /**
     * Creates a URL for the named route
     * This function accepts variable-length arguments after the name
     *
     * @param string $name The named of the route whose URL we're generating
     * @return string The generated URL if the route exists, otherwise an empty string
     * @throws URLException Thrown if there was an error generating the URL
     */
    public function createFromName($name)
    {
        $route = $this->routeCollection->getNamedRoute($name);

        if ($route === null) {
            return "";
        }

        $args = func_get_args();
        array_shift($args);

        return $this->generateHost($route, $args) . $this->generatePath($route, $args);
    }

    /**
     * Generates the host portion of a URL for a route
     *
     * @param ParsedRoute $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated host value
     * @throws URLException Thrown if the generated host is not valid
     */
    private function generateHost(ParsedRoute $route, &$values)
    {
        $host = "";

        if (!empty($route->getRawHost())) {
            $host = $this->generateUrlPart($route->getRawHost(), $route->getHostRegex(), $route->getName(), $values);
            // Prefix the URL with the protocol
            $host = "http" . ($route->isSecure() ? "s" : "") . "://" . $host;
        }

        return $host;
    }

    /**
     * Generates the path portion of a URL for a route
     *
     * @param ParsedRoute $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated path value
     * @throws URLException Thrown if the generated path is not valid
     */
    private function generatePath(ParsedRoute $route, &$values)
    {
        return $this->generateUrlPart($route->getRawPath(), $route->getPathRegex(), $route->getName(), $values);
    }

    /**
     * Generates a part of a URL for a route
     *
     * @param string $rawPart The raw part to generate
     * @param string $regex The regex to match against
     * @param string $routeName The route name
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated URL part
     * @throws UrlException Thrown if the generated path is not valid
     */
    private function generateUrlPart($rawPart, $regex, $routeName, &$values)
    {
        $generatedPart = $rawPart;
        $count = 1000;

        while ($count > 0 && count($values) > 0) {
            $generatedPart = preg_replace(self::$variableMatchingRegex, $values[0], $generatedPart, 1, $count);

            if ($count > 0) {
                // Only remove a value if we actually replaced something
                array_shift($values);
            }
        }

        // Remove any leftover brackets or variables
        $generatedPart = preg_replace(self::$leftoverBracketsAndVariablesRegex, "", $generatedPart);

        // Make sure what we just generated satisfies the regex
        if (!preg_match($regex, $generatedPart)) {
            throw new UrlException(
                "Generated URL part \"$generatedPart\" does not satisfy regex for route \"$routeName\""
            );
        }

        return $generatedPart;
    }
}
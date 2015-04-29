<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a route compiler
 */
namespace RDev\Routing\Compilers;
use RDev\HTTP\Requests\Request;
use RDev\Routing\Compilers\Parsers\IParser;
use RDev\Routing\Routes\CompiledRoute;
use RDev\Routing\Routes\Route;

class Compiler implements ICompiler
{
    /** @var IParser The route parser */
    private $parser = null;

    /**
     * @param IParser $parser The route parser
     */
    public function __construct(IParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Route $route, Request $request)
    {
        $parsedRoute = $this->parser->parse($route);
        $hostMatches = [];
        $pathMatches = [];

        if(
            (($route->isSecure() && $request->isSecure()) || !$route->isSecure()) &&
            preg_match($parsedRoute->getHostRegex(), $request->getHeaders()->get("HOST"), $hostMatches) &&
            preg_match($parsedRoute->getPathRegex(), $request->getPath(), $pathMatches)
        )
        {
            // Remove the subjects
            array_shift($hostMatches);
            array_shift($pathMatches);
            $pathVariables = array_merge($hostMatches, $pathMatches);

            return new CompiledRoute($parsedRoute, true, $pathVariables);
        }
        else
        {
            return new CompiledRoute($parsedRoute, false);
        }
    }
}
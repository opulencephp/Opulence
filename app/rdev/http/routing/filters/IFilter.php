<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for route filters to implement
 */
namespace RDev\HTTP\Routing\Filters;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Routes;

interface IFilter
{
    /**
     * Runs the filter
     *
     * @param Routes\CompiledRoute $route The route that is calling the filter
     * @param Requests\Request $request The current HTTP request
     * @param Responses\Response|null $response The response if this filter is being used as a post-filter, otherwise null
     * @return Responses\Response|null The response if the filter must issue one, otherwise null
     */
    public function run(Routes\CompiledRoute $route, Requests\Request $request, Responses\Response $response = null);
}
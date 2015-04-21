<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a non-RDev controller
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;

class NonRDevController
{
    /** @var Request The HTTP request */
    private $request = null;

    /**
     * @param Request $request The HTTP request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets a custom HTTP error response
     *
     * @param int $statusCode The status code
     * @return Response The response
     */
    public function customHTTPError($statusCode)
    {
        return new Response("Error: $statusCode", $statusCode);
    }

    /**
     * Gets the index response
     *
     * @param string $id The Id from the path
     * @return Response The response
     */
    public function index($id)
    {
        return new Response("Id: $id");
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a non-Opulence controller
 */
namespace Opulence\Tests\Routing\Mocks;

use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;

class NonOpulenceController
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

    /**
     * Gets a response with "foo"
     *
     * @return Response The response
     */
    public function showFoo()
    {
        return new Response("foo");
    }
}
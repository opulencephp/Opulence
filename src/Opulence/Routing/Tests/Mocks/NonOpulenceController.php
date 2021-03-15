<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Mocks;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Mocks a non-Opulence controller
 */
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
    public function customHttpError($statusCode)
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
        return new Response('foo');
    }
}

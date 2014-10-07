<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods and properties of the HTTP protocol, including things like the request and response
 */
namespace RDev\Models\HTTP;

class Connection
{
    /** @var Request The HTTP request made by the user */
    private $request = null;
    /** @var Response The HTTP response sent to the user */
    private $response = null;

    public function __construct()
    {
        $this->request = new Request($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
        $this->response = new Response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
} 
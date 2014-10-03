<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an HTTP request
 */
namespace RDev\Models\Web;

class Request
{
    /** The delete method */
    const METHOD_DELETE = "DELETE";
    /** The get method */
    const METHOD_GET = "GET";
    /** The post method */
    const METHOD_POST = "POST";
    /** The put method */
    const METHOD_PUT = "PUT";
    /** The head method */
    const METHOD_HEAD = "HEAD";
    /** The trace method */
    const METHOD_TRACE = "TRACE";
    /** The purge method */
    const METHOD_PURGE = "PURGE";
    /** The connect method */
    const METHOD_CONNECT = "CONNECT";
    /** The patch method */
    const METHOD_PATCH = "PATCH";
    /** The options method */
    const METHOD_OPTIONS = "OPTIONS";

    /** @var string The method used in the request */
    private $method = "";
    /** @var string The client's IP address */
    private $ipAddress = "";
    /** @var RequestParameters The list of GET parameters */
    private $query = null;
    /** @var RequestParameters The list of POST parameters */
    private $post = null;
    /** @var Headers The list of headers */
    private $headers = null;
    /** @var RequestParameters The list of SERVER parameters */
    private $server = null;
    /** @var RequestParameters The list of FILES parameters */
    private $files = null;
    /** @var RequestParameters The list of cookies */
    private $cookies = null;

    /**
     * @param array $query The GET parameters
     * @param array $post The POST parameters
     * @param array $cookies The COOKIE parameters
     * @param array $server The SERVER parameters
     * @param array $files The FILES parameters
     */
    public function __construct(array $query, array $post, array $cookies, array $server, array $files)
    {
        $this->query = new RequestParameters($query);
        $this->post = new RequestParameters($post);
        $this->cookies = new RequestParameters($cookies);
        $this->server = new RequestParameters($server);
        $this->headers = new Headers($server);
        $this->files = new RequestParameters($files);
        $this->setMethod();
        $this->setIPAddress();
    }

    /**
     * @return RequestParameters
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return RequestParameters
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Gets the method used in the request
     *
     * @return string The method used in the request
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return RequestParameters
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return RequestParameters
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return RequestParameters
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Sets the IP address attribute
     */
    private function setIPAddress()
    {
        $ipKeys = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP",
            "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR"];

        foreach($ipKeys as $key)
        {
            if($this->server->has($key))
            {
                foreach(explode(",", $this->server->get($key)) as $ipAddress)
                {
                    $ipAddress = trim($ipAddress);

                    if(filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE
                            | FILTER_FLAG_NO_RES_RANGE) !== false
                    )
                    {
                        $this->ipAddress = $ipAddress;

                        return;
                    }
                }
            }
        }

        $this->ipAddress = $this->server->has("REMOTE_ADDR") ? $this->server->get("REMOTE_ADDR") : "";
    }

    /**
     * Sets the method
     */
    private function setMethod()
    {
        if($this->server->has("REQUEST_METHOD"))
        {
            switch(strtolower($this->server->get("REQUEST_METHOD")))
            {
                case "delete":
                    $this->method = self::METHOD_DELETE;
                    break;
                case "get":
                    $this->method = self::METHOD_GET;
                    break;
                case "post":
                    $this->method = self::METHOD_POST;
                    break;
                case "put":
                    $this->method = self::METHOD_PUT;
                    break;
                case "head":
                    $this->method = self::METHOD_HEAD;
                    break;
                case "trace":
                    $this->method = self::METHOD_TRACE;
                    break;
                case "purge":
                    $this->method = self::METHOD_PURGE;
                    break;
                case "connect":
                    $this->method = self::METHOD_CONNECT;
                    break;
                case "patch":
                    $this->method = self::METHOD_PATCH;
                    break;
                case "options":
                    $this->method = self::METHOD_OPTIONS;
                    break;
                default:
                    $this->method = self::METHOD_GET;
                    break;
            }
        }
        else
        {
            $this->method = self::METHOD_GET;
        }
    }
} 
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
    /** @var string The client's user agent */
    private $userAgent = "";

    public function __construct()
    {
        $this->setMethod();
        $this->setIPAddress();
        $this->setUserAgent();
    }

    /**
     * Gets whether or not a cookie is set to a non-empty value
     *
     * @param string $name The name of the cookie to check
     * @return bool True if the cookie has a non-empty value, otherwise false
     */
    public function cookieIsSet($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Gets the value of a cookie
     *
     * @param string $name The name of the cookie to check
     * @return mixed|bool The cookie if it was set, otherwise false
     */
    public function getCookie($name)
    {
        if(!$this->cookieIsSet($name))
        {
            return false;
        }

        return $_COOKIE[$name];
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
     * Gets the value of a post variable
     *
     * @param string $name The name of the variable to check
     * @return mixed|bool The post variable if it was set, otherwise false
     */
    public function getPostVar($name)
    {
        if(!$this->postVarIsSet($name))
        {
            return false;
        }

        return $_POST[$name];
    }

    /**
     * Gets the value of a query string variable
     *
     * @param string $name The name of the variable to check
     * @return mixed|bool The query string variable if it was set, otherwise false
     */
    public function getQueryStringVar($name)
    {
        if(!$this->queryStringVarIsSet($name))
        {
            return false;
        }

        return $_GET[$name];
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Gets whether or not a post variable is set to a non-empty value
     *
     * @param string $name The name of the variable to check
     * @return bool True if the post variable has a non-empty value, otherwise false
     */
    public function postVarIsSet($name)
    {
        return isset($_POST[$name]);
    }

    /**
     * Gets whether or not a query string variable is set to a non-empty value
     *
     * @param string $name The name of the variable to check
     * @return bool True if the query string variable has a non-empty value, otherwise false
     */
    public function queryStringVarIsSet($name)
    {
        return isset($_GET[$name]);
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
            if(array_key_exists($key, $_SERVER))
            {
                foreach(explode(",", $_SERVER[$key]) as $ipAddress)
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

        $this->ipAddress = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
    }

    /**
     * Sets the method
     */
    private function setMethod()
    {
        if(isset($_SERVER["REQUEST_METHOD"]))
        {
            switch(strtolower($_SERVER["REQUEST_METHOD"]))
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

    /**
     * Sets the user agent attribute
     */
    private function setUserAgent()
    {
        $this->userAgent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
    }
} 
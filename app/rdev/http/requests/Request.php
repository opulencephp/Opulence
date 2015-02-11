<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an HTTP request
 */
namespace RDev\HTTP\Requests;
use RDev\HTTP;

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
    /** @var HTTP\Parameters The list of GET parameters */
    private $query = null;
    /** @var HTTP\Parameters The list of POST parameters */
    private $post = null;
    /** @var HTTP\Parameters The list of PUT parameters */
    private $put = null;
    /** @var HTTP\Parameters The list of PATCH parameters */
    private $patch = null;
    /** @var HTTP\Parameters The list of DELETE parameters */
    private $delete = null;
    /** @var HTTP\Headers The list of headers */
    private $headers = null;
    /** @var HTTP\Parameters The list of SERVER parameters */
    private $server = null;
    /** @var HTTP\Parameters The list of FILES parameters */
    private $files = null;
    /** @var HTTP\Parameters The list of ENV parameters */
    private $env = null;
    /** @var HTTP\Parameters The list of cookies */
    private $cookies = null;
    /** @var string The path of the request, which does not include the query string */
    private $path = "";
    /** @var string The raw body of the request */
    private $rawBody = null;

    /**
     * @param array $query The GET parameters
     * @param array $post The POST parameters
     * @param array $cookies The COOKIE parameters
     * @param array $server The SERVER parameters
     * @param array $files The FILES parameters
     * @param array $env The ENV parameters
     */
    public function __construct(array $query, array $post, array $cookies, array $server, array $files, array $env)
    {
        $this->query = new HTTP\Parameters($query);
        $this->post = new HTTP\Parameters($post);
        $this->put = new HTTP\Parameters([]);
        $this->patch = new HTTP\Parameters([]);
        $this->delete = new HTTP\Parameters([]);
        $this->cookies = new HTTP\Parameters($cookies);
        $this->server = new HTTP\Parameters($server);
        $this->headers = new HTTP\Headers($server);
        $this->files = new HTTP\Parameters($files);
        $this->env = new HTTP\Parameters($env);
        $this->setMethod();
        $this->setIPAddress();
        $this->setPath();
        // This must go here because it relies on other things being set first
        $this->setUnsupportedMethodsParameters();
    }

    /**
     * Creates an instance of this class using the PHP globals
     *
     * @return Request An instance of this class
     */
    public static function createFromGlobals()
    {
        // Handle the a bug that does not set CONTENT_TYPE or CONTENT_LENGTH headers
        if(array_key_exists("HTTP_CONTENT_LENGTH", $_SERVER))
        {
            $_SERVER["CONTENT_LENGTH"] = $_SERVER["HTTP_CONTENT_LENGTH"];
        }

        if(array_key_exists("HTTP_CONTENT_TYPE", $_SERVER))
        {
            $_SERVER["CONTENT_TYPE"] = $_SERVER["HTTP_CONTENT_TYPE"];
        }

        return new static($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV);
    }

    /**
     * @return HTTP\Parameters
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return HTTP\Headers
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
     * Gets the raw body as a JSON array
     *
     * @return array The JSON-decoded body
     * @throws HTTP\HTTPException Thrown if the body could not be decoded
     */
    public function getJSONBody()
    {
        $json = json_decode($this->getRawBody(), true);

        if($json === null)
        {
            throw new HTTP\HTTPException("Body could not be decoded as JSON");
        }

        return $json;
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
     * @return HTTP\Parameters
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getPut()
    {
        return $this->put;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Gets the raw body
     *
     * @return string The raw body
     */
    public function getRawBody()
    {
        if($this->rawBody === null)
        {
            $this->rawBody = file_get_contents("php://input");
        }

        return $this->rawBody;
    }

    /**
     * @return HTTP\Parameters
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets whether or not a call was made by AJAX
     *
     * @return bool True if the request was made by AJAX, otherwise false
     */
    public function isAJAX()
    {
        return $this->headers->get("X_REQUESTED_WITH") == "XMLHttpRequest";
    }

    /**
     * Gets whether or not the current path matches the input path or regular expression
     *
     * @param string $path The path or regular expression to match against
     *      If the path is a regular expression, it should not include regex delimiters
     * @param bool $isRegex True if the path is a regular expression, otherwise false
     * @return bool True if the current path matched the path, otherwise false
     */
    public function isPath($path, $isRegex = false)
    {
        if($isRegex)
        {
            return preg_match("#^" . $path . "$#", $this->path) === 1;
        }
        else
        {
            return $this->path == $path;
        }
    }

    /**
     * Gets whether or not the request was made through HTTPS
     *
     * @return bool True if the request is secure, otherwise false
     */
    public function isSecure()
    {
        return $this->server->has("HTTPS") && $this->server->get("HTTPS") !== "off";
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

        $this->ipAddress = $this->server->get("REMOTE_ADDR", "");
    }

    /**
     * Sets the method
     */
    private function setMethod()
    {
        switch(strtolower($this->server->get("REQUEST_METHOD", self::METHOD_GET)))
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

    /**
     * Sets the path of this request, which does not include the query string
     */
    private function setPath()
    {
        $uri = $this->server->get("REQUEST_URI");

        if(empty($uri))
        {
            // Default to a slash
            $this->path = "/";
        }
        else
        {
            $uriParts = explode("?", $uri);
            $this->path = $uriParts[0];
        }
    }

    /**
     * Sets PUT/PATCH/DELETE parameters, if they exist
     */
    private function setUnsupportedMethodsParameters()
    {
        /**
         * PHP doesn't pass in data from PUT/PATCH/DELETE requests through globals
         * So, we have to manually read from the input stream to grab their data
         * If the content is not from a form, we don't bother and just let users look the data up in the raw body
         */
        if(
            strpos($this->headers->get("CONTENT_TYPE"), "application/x-www-form-urlencoded") === 0 &&
            in_array($this->method, [self::METHOD_PUT, self::METHOD_PATCH, self::METHOD_DELETE])
        )
        {
            parse_str($this->getRawBody(), $parameters);

            switch($this->method)
            {
                case self::METHOD_PUT:
                    $this->put->exchangeArray($parameters);

                    break;
                case self::METHOD_PATCH:
                    $this->patch->exchangeArray($parameters);

                    break;
                case self::METHOD_DELETE:
                    $this->delete->exchangeArray($parameters);

                    break;
            }
        }
    }
} 
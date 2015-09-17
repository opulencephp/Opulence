<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an HTTP request
 */
namespace Opulence\HTTP\Requests;
use InvalidArgumentException;
use Opulence\HTTP\Headers;
use Opulence\HTTP\HTTPException;
use Opulence\HTTP\Parameters;

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
    /** @var Parameters The list of GET parameters */
    private $query = null;
    /** @var Parameters The list of POST parameters */
    private $post = null;
    /** @var Parameters The list of PUT parameters */
    private $put = null;
    /** @var Parameters The list of PATCH parameters */
    private $patch = null;
    /** @var Parameters The list of DELETE parameters */
    private $delete = null;
    /** @var Headers The list of headers */
    private $headers = null;
    /** @var Parameters The list of SERVER parameters */
    private $server = null;
    /** @var Parameters The list of FILES parameters */
    private $files = null;
    /** @var Parameters The list of ENV parameters */
    private $env = null;
    /** @var Parameters The list of cookies */
    private $cookies = null;
    /** @var string The path of the request, which does not include the query string */
    private $path = "";
    /** @var string The previous URL */
    private $previousURL = "";
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
        $this->query = new Parameters($query);
        $this->post = new Parameters($post);
        $this->put = new Parameters([]);
        $this->patch = new Parameters([]);
        $this->delete = new Parameters([]);
        $this->cookies = new Parameters($cookies);
        $this->server = new Parameters($server);
        $this->headers = new Headers($server);
        $this->files = new Parameters($files);
        $this->env = new Parameters($env);
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
     * Clones the objects in the request
     */
    public function __clone()
    {
        $this->query = clone $this->query;
        $this->post = clone $this->post;
        $this->put = clone $this->put;
        $this->patch = clone $this->patch;
        $this->delete = clone $this->delete;
        $this->cookies = clone $this->cookies;
        $this->server = clone $this->server;
        $this->headers = clone $this->headers;
        $this->files = clone $this->files;
        $this->env = clone $this->env;
    }

    /**
     * @return Parameters
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return Parameters
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @return Parameters
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return Parameters
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Gets the full URL for the current request
     *
     * @return string The full URL
     * @link http://stackoverflow.com/questions/6768793/get-the-full-url-in-php#answer-8891890
     */
    public function getFullURL()
    {
        $isSecure = $this->isSecure();
        $rawProtocol = strtolower($this->server->get("SERVER_PROTOCOL"));
        $parsedProtocol = substr($rawProtocol, 0, strpos($rawProtocol, "/")) . (($isSecure) ? "s" : "");
        $port = $this->server->get("SERVER_PORT");
        $host = $this->getHost();

        // Prepend a colon if the port is non-standard
        if(((!$isSecure && $port != "80") || ($isSecure && $port != "443")))
        {
            $port = ":$port";
        }
        else
        {
            $port = "";
        }

        return $parsedProtocol . '://' . $host . $port . $this->server->get("REQUEST_URI");
    }

    /**
     * @return Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Gets the host name
     *
     * @return string The host
     * @throws InvalidArgumentException Thrown if the host was invalid
     */
    public function getHost()
    {
        $host = $this->headers->get("X_FORWARDED_FOR");

        if($host === null)
        {
            $host = $this->headers->get("HOST");
        }

        if($host === null)
        {
            $host = $this->server->get("SERVER_NAME");
        }

        if($host === null)
        {
            // Return an empty string by default so we can do string operations on it later
            $host = $this->server->get("SERVER_ADDR", "");
        }

        // Remove the port number
        $host = strtolower(preg_replace("/:\d+$/", "", trim($host)));

        // Check for forbidden characters
        // Credit: Symfony HttpFoundation
        if(!empty($host) && !empty(preg_replace("/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/", "", $host)))
        {
            throw new InvalidArgumentException("Invalid host \"$host\"");
        }

        return $host;
    }

    /**
     * @return string
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Gets the input from either GET or POST data
     *
     * @param string $name The name of the input to get
     * @param null|mixed $default The default value to return if the input could not be found
     * @return mixed The value of the input if it was found, otherwise the default value
     */
    public function getInput($name, $default = null)
    {
        if($this->isJSON())
        {
            $json = $this->getJSONBody();

            if(array_key_exists($name, $json))
            {
                return $json[$name];
            }
            else
            {
                return $default;
            }
        }
        else
        {
            if($this->method === self::METHOD_GET)
            {
                return $this->query->get($name, $default);
            }
            else
            {
                return $this->post->get($name, $default);
            }
        }
    }

    /**
     * Gets the raw body as a JSON array
     *
     * @return array The JSON-decoded body
     * @throws HTTPException Thrown if the body could not be decoded
     */
    public function getJSONBody()
    {
        $json = json_decode($this->getRawBody(), true);

        if($json === null)
        {
            throw new HTTPException("Body could not be decoded as JSON");
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
     * Gets the auth password
     *
     * @return string|null The auth password
     */
    public function getPassword()
    {
        return $this->server->get("PHP_AUTH_PW");
    }

    /**
     * @return Parameters
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
     * @return Parameters
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * The previous URL, if one was set, otherwise the referrer header
     *
     * @param bool $fallBackToReferer True if we fall back to the HTTP referer header, otherwise false
     * @return string The previous URL
     */
    public function getPreviousURL($fallBackToReferer = true)
    {
        if(!empty($this->previousURL))
        {
            return $this->previousURL;
        }

        if($fallBackToReferer)
        {
            return $this->headers->get("REFERER");
        }

        return "";
    }

    /**
     * @return Parameters
     */
    public function getPut()
    {
        return $this->put;
    }

    /**
     * @return Parameters
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
     * @return Parameters
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets the auth user
     *
     * @return string|null The auth user
     */
    public function getUser()
    {
        return $this->server->get("PHP_AUTH_USER");
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
     * Gets whether or not the request body is JSON
     *
     * @return bool True if the request body was JSON, otherwise false
     */
    public function isJSON()
    {
        return $this->headers->get("CONTENT_TYPE") == "application/json";
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
     * Sets the method
     * If no input is specified, then it is automatically set using headers
     *
     * @param string|null $method The method to set, otherwise null to automatically set the method
     */
    public function setMethod($method = null)
    {
        if($method === null)
        {
            switch(mb_strtolower($this->server->get("REQUEST_METHOD", self::METHOD_GET)))
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
            $this->method = $method;
        }
    }

    /**
     * Sets the path of this request, which does not include the query string
     * If no input is specified, then it is automatically set using headers
     *
     * @param string|null $path The path to set, otherwise null to automatically set the path
     */
    public function setPath($path = null)
    {
        if($path === null)
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
        else
        {
            $this->path = $path;
        }
    }

    /**
     * Sets the previous URL
     *
     * @param string $previousURL The previous URL
     */
    public function setPreviousURL($previousURL)
    {
        $this->previousURL = $previousURL;
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
            mb_strpos($this->headers->get("CONTENT_TYPE"), "application/x-www-form-urlencoded") === 0 &&
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
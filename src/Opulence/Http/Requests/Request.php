<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Requests;

use InvalidArgumentException;
use Opulence\Http\Headers;
use Opulence\Http\Collection;
use Opulence\Tests\Http\Requests\Mocks\UploadedFile;
use RuntimeException;

/**
 * Defines an HTTP request
 */
class Request
{
    /** @var array The list of valid methods */
    private static $validMethods = [
        RequestMethods::DELETE,
        RequestMethods::GET,
        RequestMethods::POST,
        RequestMethods::PUT,
        RequestMethods::HEAD,
        RequestMethods::TRACE,
        RequestMethods::PURGE,
        RequestMethods::CONNECT,
        RequestMethods::PATCH,
        RequestMethods::OPTIONS
    ];
    /** @var array The list of trusted proxy Ips */
    private static $trustedProxies = [];
    /** @var array The list of trusted headers */
    private static $trustedHeaderNames = [
        RequestHeaders::FORWARDED => "FORWARDED",
        RequestHeaders::CLIENT_IP => "X_FORWARDED_FOR",
        RequestHeaders::CLIENT_HOST => "X_FORWARDED_HOST",
        RequestHeaders::CLIENT_PORT => "X_FORWARDED_PORT",
        RequestHeaders::CLIENT_PROTO => "X_FORWARDED_PROTO"
    ];
    /** @var string The method used in the request */
    private $method = "";
    /** @var array The client's IP addresses */
    private $clientIPAddresses = [];
    /** @var Collection The list of GET parameters */
    private $query = null;
    /** @var Collection The list of POST parameters */
    private $post = null;
    /** @var Collection The list of PUT parameters */
    private $put = null;
    /** @var Collection The list of PATCH parameters */
    private $patch = null;
    /** @var Collection The list of DELETE parameters */
    private $delete = null;
    /** @var Headers The list of headers */
    private $headers = null;
    /** @var Collection The list of SERVER parameters */
    private $server = null;
    /** @var Collection The list of FILES parameters */
    private $files = null;
    /** @var Collection The list of ENV parameters */
    private $env = null;
    /** @var Collection The list of cookies */
    private $cookies = null;
    /** @var string The path of the request, which does not include the query string */
    private $path = "";
    /** @var string The previous URL */
    private $previousUrl = "";
    /** @var string The raw body of the request */
    private $rawBody = null;

    /**
     * @param array $query The GET parameters
     * @param array $post The POST parameters
     * @param array $cookies The COOKIE parameters
     * @param array $server The SERVER parameters
     * @param array $files The FILES parameters
     * @param array $env The ENV parameters
     * @param string|null $rawBody The raw body
     */
    public function __construct(
        array $query,
        array $post,
        array $cookies,
        array $server,
        array $files,
        array $env,
        string $rawBody = null
    ) {
        $this->query = new Collection($query);
        $this->post = new Collection($post);
        $this->put = new Collection([]);
        $this->patch = new Collection([]);
        $this->delete = new Collection([]);
        $this->cookies = new Collection($cookies);
        $this->server = new Collection($server);
        $this->headers = new Headers($server);
        $this->files = new Files($files);
        $this->env = new Collection($env);
        $this->rawBody = $rawBody;
        $this->setMethod();
        $this->setClientIPAddresses();
        $this->setPath();
        // This must go here because it relies on other things being set first
        $this->setUnsupportedMethodsCollections();
    }

    /**
     * Creates an instance of this class using the PHP globals
     *
     * @param array|null $query The GET parameters, or null if using the globals
     * @param array|null $post The POST parameters, or null if using the globals
     * @param array|null $cookies The COOKIE parameters, or null if using the globals
     * @param array|null $server The SERVER parameters, or null if using the globals
     * @param array|null $files The FILES parameters, or null if using the globals
     * @param array|null $env The ENV parameters, or null if using the globals
     * @param string|null $rawBody The raw body
     * @return Request An instance of this class
     */
    public static function createFromGlobals(
        array $query = null,
        array $post = null,
        array $cookies = null,
        array $server = null,
        array $files = null,
        array $env = null,
        string $rawBody = null
    ) : Request
    {
        $query = isset($query) ? $query : $_GET;
        $post = isset($post) ? $post : $_POST;
        $cookies = isset($cookies) ? $cookies : $_COOKIE;
        $server = isset($server) ? $server : $_SERVER;
        $files = isset($files) ? $files : $_FILES;
        $env = isset($env) ? $env : $_ENV;

        // Handle the a bug that does not set CONTENT_TYPE or CONTENT_LENGTH headers
        if (array_key_exists("HTTP_CONTENT_LENGTH", $server)) {
            $server["CONTENT_LENGTH"] = $server["HTTP_CONTENT_LENGTH"];
        }

        if (array_key_exists("HTTP_CONTENT_TYPE", $server)) {
            $server["CONTENT_TYPE"] = $server["HTTP_CONTENT_TYPE"];
        }

        return new static($query, $post, $cookies, $server, $files, $env, $rawBody);
    }

    /**
     * Creates an instance of this class from a URL
     *
     * @param string $url The URL
     * @param string $method The HTTP method
     * @param array $parameters The parameters (will be bound to query if GET request, otherwise bound to post)
     * @param array $cookies The COOKIE parameters
     * @param array $server The SERVER parameters
     * @param UploadedFile[] $files The list of uploaded files
     * @param array $env The ENV parameters
     * @param string|null $rawBody The raw body
     * @return Request An instance of this class
     */
    public static function createFromUrl(
        string $url,
        string $method,
        array $parameters = [],
        array $cookies = [],
        array $server = [],
        array $files = [],
        array $env = [],
        string $rawBody = null
    ) : Request
    {
        // Define some basic server vars, but override them with with input on collision
        $server = array_replace(
            [
                "HTTP_ACCEPT" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "HTTP_HOST" => "localhost",
                "REMOTE_ADDR" => "127.0.01",
                "SCRIPT_FILENAME" => "",
                "SCRIPT_NAME" => "",
                "SERVER_NAME" => "localhost",
                "SERVER_PORT" => 80,
                "SERVER_PROTOCOL" => "HTTP/1.1"
            ],
            $server
        );

        $query = [];
        $post = [];

        // Set the content type for unsupported HTTP methods
        if ($method == RequestMethods::GET) {
            $query = $parameters;
        } elseif ($method == RequestMethods::POST) {
            $post = $parameters;
        } elseif (
            in_array($method, [RequestMethods::PUT, RequestMethods::PATCH, RequestMethods::DELETE]) &&
            !isset($server["CONTENT_TYPE"])
        ) {
            $server["CONTENT_TYPE"] = "application/x-www-form-urlencoded";
        }

        $server["REQUEST_METHOD"] = $method;
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl["host"])) {
            $server["HTTP_HOST"] = $parsedUrl["host"];
        }

        if (isset($parsedUrl["path"])) {
            $server["REQUEST_URI"] = $parsedUrl["path"];
        }

        if (isset($parsedUrl["query"])) {
            parse_str(html_entity_decode($parsedUrl["query"]), $queryFromUrl);
            $query = array_replace($queryFromUrl, $query);
        }

        $queryString = http_build_query($query, "", "&");
        $server["QUERY_STRING"] = $queryString;
        $server["REQUEST_URI"] .= count($query) > 0 ? "?$queryString" : "";

        if (isset($parsedUrl["scheme"])) {
            if ($parsedUrl["scheme"] == "https") {
                $server["HTTPS"] = "on";
                $server["SERVER_PORT"] = 443;
            } else {
                unset($server["HTTPS"]);
                $server["SERVER_PORT"] = 80;
            }
        }

        if (isset($parsedUrl["port"])) {
            $server["SERVER_PORT"] = $parsedUrl["port"];
            $server["HTTP_HOST"] .= ":{$parsedUrl["port"]}";
        }

        $parsedFiles = [];

        foreach ($files as $file) {
            $parsedFiles[] = [
                "tmp_name" => $file->getFilename(),
                "name" => $file->getTempFilename(),
                "size" => $file->getTempSize(),
                "type" => $file->getTempMimeType(),
                "error" => $file->getError()
            ];
        }

        return new static($query, $post, $cookies, $server, $parsedFiles, $env, $rawBody);
    }

    /**
     * Sets a trusted header name
     *
     * @param string $name The name of the header
     * @param mixed $value The value of the header
     */
    public static function setTrustedHeaderName(string $name, $value)
    {
        self::$trustedHeaderNames[$name] = $value;
    }

    /**
     * Sets the list of trusted proxy Ips
     *
     * @param array|string $trustedProxies The list of trusted proxies
     */
    public static function setTrustedProxies($trustedProxies)
    {
        self::$trustedProxies = (array)$trustedProxies;
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
     * @return string
     */
    public function getClientIPAddress() : string
    {
        return $this->clientIPAddresses[0];
    }

    /**
     * @return Collection
     */
    public function getCookies() : Collection
    {
        return $this->cookies;
    }

    /**
     * @return Collection
     */
    public function getDelete() : Collection
    {
        return $this->delete;
    }

    /**
     * @return Collection
     */
    public function getEnv() : Collection
    {
        return $this->env;
    }

    /**
     * @return Files
     */
    public function getFiles() : Files
    {
        return $this->files;
    }

    /**
     * Gets the full URL for the current request
     *
     * @return string The full URL
     * @link http://stackoverflow.com/questions/6768793/get-the-full-url-in-php#answer-8891890
     */
    public function getFullUrl() : string
    {
        $isSecure = $this->isSecure();
        $rawProtocol = strtolower($this->server->get("SERVER_PROTOCOL"));
        $parsedProtocol = substr($rawProtocol, 0, strpos($rawProtocol, "/")) . (($isSecure) ? "s" : "");
        $port = $this->getPort();
        $host = $this->getHost();

        // Prepend a colon if the port is non-standard
        if (((!$isSecure && $port != "80") || ($isSecure && $port != "443"))) {
            $port = ":$port";
        } else {
            $port = "";
        }

        return $parsedProtocol . '://' . $host . $port . $this->server->get("REQUEST_URI");
    }

    /**
     * @return Headers
     */
    public function getHeaders() : Headers
    {
        return $this->headers;
    }

    /**
     * Gets the host name
     *
     * @return string The host
     * @throws InvalidArgumentException Thrown if the host was invalid
     */
    public function getHost() : string
    {
        if ($this->isUsingTrustedProxy() && $this->headers->has(self::$trustedHeaderNames[RequestHeaders::CLIENT_HOST])) {
            $hosts = explode(",", $this->headers->get(self::$trustedHeaderNames[RequestHeaders::CLIENT_HOST]));
            $host = trim(end($hosts));
        } else {
            $host = $this->headers->get("X_FORWARDED_FOR");
        }

        if ($host === null) {
            $host = $this->headers->get("HOST");
        }

        if ($host === null) {
            $host = $this->server->get("SERVER_NAME");
        }

        if ($host === null) {
            // Return an empty string by default so we can do string operations on it later
            $host = $this->server->get("SERVER_ADDR", "");
        }

        // Remove the port number
        $host = strtolower(preg_replace("/:\d+$/", "", trim($host)));

        // Check for forbidden characters
        // Credit: Symfony HTTPFoundation
        if (!empty($host) && !empty(preg_replace("/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/", "", $host))) {
            throw new InvalidArgumentException("Invalid host \"$host\"");
        }

        return $host;
    }

    /**
     * Gets the input from either GET or POST data
     *
     * @param string $name The name of the input to get
     * @param null|mixed $default The default value to return if the input could not be found
     * @return mixed The value of the input if it was found, otherwise the default value
     */
    public function getInput(string $name, $default = null)
    {
        if ($this->isJson()) {
            $json = $this->getJsonBody();

            if (array_key_exists($name, $json)) {
                return $json[$name];
            } else {
                return $default;
            }
        } else {
            $value = null;

            switch ($this->method) {
                case RequestMethods::GET:
                    return $this->query->get($name, $default);
                case RequestMethods::POST:
                    $value = $this->post->get($name, $default);
                    break;
                case RequestMethods::DELETE:
                    $value = $this->delete->get($name, $default);
                    break;
                case RequestMethods::PUT:
                    $value = $this->put->get($name, $default);
                    break;
                case RequestMethods::PATCH:
                    $value = $this->patch->get($name, $default);
                    break;
            }

            if ($value === null) {
                // Try falling back to query
                $value = $this->query->get($name, $default);
            }

            return $value;
        }
    }

    /**
     * Gets the raw body as a JSON array
     *
     * @return array The JSON-decoded body
     * @throws RuntimeException Thrown if the body could not be decoded
     */
    public function getJsonBody() : array
    {
        $json = json_decode($this->getRawBody(), true);

        if ($json === null) {
            throw new RuntimeException("Body could not be decoded as JSON");
        }

        return $json;
    }

    /**
     * Gets the method used in the request
     *
     * @return string The method used in the request
     */
    public function getMethod() : string
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
     * @return Collection
     */
    public function getPatch() : Collection
    {
        return $this->patch;
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Gets the port number
     *
     * @return int The port number
     */
    public function getPort() : int
    {
        if ($this->isUsingTrustedProxy()) {
            if ($this->server->has(self::$trustedHeaderNames[RequestHeaders::CLIENT_PORT])) {
                return (int)$this->server->get(self::$trustedHeaderNames[RequestHeaders::CLIENT_PORT]);
            } elseif ($this->server->get(self::$trustedHeaderNames[RequestHeaders::CLIENT_PROTO]) === "https") {
                return 443;
            }
        }

        return (int)$this->server->get("SERVER_PORT");

    }

    /**
     * @return Collection
     */
    public function getPost() : Collection
    {
        return $this->post;
    }

    /**
     * The previous URL, if one was set, otherwise the referrer header
     *
     * @param bool $fallBackToReferer True if we fall back to the HTTP referer header, otherwise false
     * @return string The previous URL
     */
    public function getPreviousUrl(bool $fallBackToReferer = true) : string
    {
        if (!empty($this->previousUrl)) {
            return $this->previousUrl;
        }

        if ($fallBackToReferer) {
            return $this->headers->get("REFERER", "");
        }

        return "";
    }

    /**
     * @return Collection
     */
    public function getPut() : Collection
    {
        return $this->put;
    }

    /**
     * @return Collection
     */
    public function getQuery() : Collection
    {
        return $this->query;
    }

    /**
     * Gets the raw body
     *
     * @return string The raw body
     */
    public function getRawBody() : string
    {
        if ($this->rawBody === null) {
            $this->rawBody = file_get_contents("php://input");
        }

        return $this->rawBody;
    }

    /**
     * @return Collection
     */
    public function getServer() : Collection
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
    public function isAjax() : bool
    {
        return $this->headers->get("X_REQUESTED_WITH") == "XMLHttpRequest";
    }

    /**
     * Gets whether or not the request body is JSON
     *
     * @return bool True if the request body was JSON, otherwise false
     */
    public function isJson() : bool
    {
        return preg_match("/application\/json/i", $this->headers->get("CONTENT_TYPE")) === 1;
    }

    /**
     * Gets whether or not the current path matches the input path or regular expression
     *
     * @param string $path The path or regular expression to match against
     *      If the path is a regular expression, it should not include regex delimiters
     * @param bool $isRegex True if the path is a regular expression, otherwise false
     * @return bool True if the current path matched the path, otherwise false
     */
    public function isPath(string $path, bool $isRegex = false) : bool
    {
        if ($isRegex) {
            return preg_match("#^" . $path . "$#", $this->path) === 1;
        } else {
            return $this->path == $path;
        }
    }

    /**
     * Gets whether or not the request was made through HTTPS
     *
     * @return bool True if the request is secure, otherwise false
     */
    public function isSecure() : bool
    {
        if ($this->isUsingTrustedProxy() && $this->server->has(self::$trustedHeaderNames[RequestHeaders::CLIENT_PROTO])) {
            $protoString = $this->server->get(self::$trustedHeaderNames[RequestHeaders::CLIENT_PROTO]);
            $protoArray = explode(",", $protoString);

            return count($protoArray) > 0 && in_array(strtolower($protoArray[0]), ["https", "ssl", "on"]);
        }

        return $this->server->has("HTTPS") && $this->server->get("HTTPS") !== "off";
    }

    /**
     * Gets whether or not the current URL matches the input URL or regular expression
     *
     * @param string $url The URL or regular expression to match against
     *      If the URL is a regular expression, it should not include regex delimiters
     * @param bool $isRegex True if the URL is a regular expression, otherwise false
     * @return bool True if the current URL matched the URL, otherwise false
     */
    public function isUrl(string $url, bool $isRegex = false) : bool
    {
        if ($isRegex) {
            return preg_match("#^" . $url . "$#", $this->getFullUrl()) === 1;
        } else {
            return $this->getFullUrl() == $url;
        }
    }

    /**
     * Sets the method
     * If no input is specified, then it is automatically set using headers
     *
     * @param string|null $method The method to set, otherwise null to automatically set the method
     * @throws InvalidArgumentException Thrown if the method is not an acceptable one
     */
    public function setMethod(string $method = null)
    {
        if ($method === null) {
            $method = $this->server->get("REQUEST_METHOD", RequestMethods::GET);

            if ($method == RequestMethods::POST) {
                if (($overrideMethod = $this->server->get("X-HTTP-METHOD-OVERRIDE")) !== null) {
                    $method = $overrideMethod;
                } else {
                    $method = $this->post->get("_method", $this->query->get("_method", $method));
                }
            }
        }

        if (!is_string($method)) {
            throw new InvalidArgumentException(
                sprintf(
                    'HTTP method must be string, %s provided',
                    is_object($method) ? get_class($method) : gettype($method)
                )
            );
        }

        $method = strtoupper($method);

        if (!in_array($method, self::$validMethods)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid HTTP method "%s"',
                    $method
                )
            );
        }

        $this->method = $method;
    }

    /**
     * Sets the path of this request, which does not include the query string
     * If no input is specified, then it is automatically set using headers
     *
     * @param string|null $path The path to set, otherwise null to automatically set the path
     */
    public function setPath(string $path = null)
    {
        if ($path === null) {
            $uri = $this->server->get("REQUEST_URI");

            if (empty($uri)) {
                // Default to a slash
                $this->path = "/";
            } else {
                $uriParts = explode("?", $uri);
                $this->path = $uriParts[0];
            }
        } else {
            $this->path = $path;
        }
    }

    /**
     * Sets the previous URL
     *
     * @param string $previousUrl The previous URL
     */
    public function setPreviousUrl(string $previousUrl)
    {
        $this->previousUrl = $previousUrl;
    }

    /**
     * Gets whether or not we're using a trusted proxy
     *
     * @return bool True if using a trusted proxy, otherwise false
     */
    private function isUsingTrustedProxy() : bool
    {
        return in_array($this->server->get("REMOTE_ADDR"), self::$trustedProxies);
    }

    /**
     * Sets the client IP addresses
     */
    private function setClientIPAddresses()
    {
        if ($this->isUsingTrustedProxy()) {
            $this->clientIPAddresses = [$this->server->get("REMOTE_ADDR")];
        } else {
            $ipAddresses = [];

            // RFC 7239
            if ($this->headers->has(self::$trustedHeaderNames[RequestHeaders::FORWARDED])) {
                $header = $this->headers->get(self::$trustedHeaderNames[RequestHeaders::FORWARDED]);
                preg_match_all("/for=(?:\"?\[?)([a-z0-9:\.\-\/_]*)/", $header, $matches);
                $ipAddresses = $matches[1];
            } elseif ($this->headers->has(self::$trustedHeaderNames[RequestHeaders::CLIENT_IP])) {
                $ipAddresses = explode(",", $this->headers->get(self::$trustedHeaderNames[RequestHeaders::CLIENT_IP]));
                $ipAddresses = array_map("trim", $ipAddresses);
            }

            $ipAddresses[] = $this->server->get("REMOTE_ADDR");
            $fallbackIpAddresses = [$ipAddresses[0]];

            foreach ($ipAddresses as $index => $ipAddress) {
                // Check for valid IP address
                if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
                    unset($ipAddresses[$index]);
                }

                // Don't accept trusted proxies
                if (in_array($ipAddress, self::$trustedProxies)) {
                    unset($ipAddresses[$index]);
                }
            }

            $this->clientIPAddresses = count($ipAddresses) == 0 ? $fallbackIpAddresses : array_reverse($ipAddresses);
        }
    }

    /**
     * Sets PUT/PATCH/DELETE collections, if they exist
     */
    private function setUnsupportedMethodsCollections()
    {
        /**
         * PHP doesn't pass in data from PUT/PATCH/DELETE requests through globals
         * So, we have to manually read from the input stream to grab their data
         * If the content is not from a form, we don't bother and just let users look the data up in the raw body
         */
        if (
            mb_strpos($this->headers->get("CONTENT_TYPE"), "application/x-www-form-urlencoded") === 0 &&
            in_array($this->method, [RequestMethods::PUT, RequestMethods::PATCH, RequestMethods::DELETE])
        ) {
            parse_str($this->getRawBody(), $collection);

            switch ($this->method) {
                case RequestMethods::PUT:
                    $this->put->exchangeArray($collection);
                    break;
                case RequestMethods::PATCH:
                    $this->patch->exchangeArray($collection);
                    break;
                case RequestMethods::DELETE:
                    $this->delete->exchangeArray($collection);
                    break;
            }
        }
    }
} 
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use InvalidArgumentException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\UploadedFile;

/**
 * Defines the request builder for HTTP applications
 */
class RequestBuilder
{
    /** @var IntegrationTestCase The integration test that created this builder */
    private $integrationTest = null;
    /** @var string|null The URL of the request */
    private $url = null;
    /** @var string|null The HTTP method of the request */
    private $method = null;
    /** @var array The headers */
    private $server = [];
    /** @var array The parameters */
    private $parameters = [];
    /** @var array The cookies */
    private $cookies = [];
    /** @var UploadedFile[] The files */
    private $files = [];
    /** @var array The environment parameters */
    private $env = [];
    /** @var mixed|null The raw body */
    private $rawBody = null;

    /**
     * @param IntegrationTestCase $integrationTest The integration test that created this builder
     * @param string $method The HTTP method of the request
     * @param string|null $url The URL of the request
     */
    public function __construct(IntegrationTestCase $integrationTest, $method, $url = null)
    {
        $this->integrationTest = $integrationTest;
        $this->method = strtoupper($method);

        if ($url !== null) {
            $this->to($url);
        }
    }

    /**
     * Sets the URL of the request (synonymous with to())
     *
     * @param string $url
     * @return $this For method chaining
     */
    public function from($url)
    {
        $this->to($url);
    }

    /**
     * Creates the request and routes it in the test case
     *
     * @return IntegrationTestCase The application test case that created this
     * @throws InvalidArgumentException Thrown if the build request is invalid
     */
    public function go()
    {
        $this->validate();

        $request = Request::createFromUrl(
            $this->url,
            $this->method,
            $this->parameters,
            $this->cookies,
            $this->server,
            $this->files,
            $this->env,
            $this->rawBody
        );
        $this->integrationTest->route($request);

        return $this->integrationTest;
    }

    /**
     * Sets the URL of the request
     *
     * @param string $url
     * @return $this For method chaining
     */
    public function to($url)
    {
        $this->url = $url;
    }

    /**
     * Adds cookies to the response
     *
     * @param array $cookies The cookies to add
     * @param bool $overwriteOld Whether or not to overwrite all old cookies
     * @return $this For method chaining
     */
    public function withCookies(array $cookies, $overwriteOld = false)
    {
        $this->addValuesToCollection($cookies, $this->cookies, $overwriteOld);

        return $this;
    }

    /**
     * Adds environment vars to the response
     *
     * @param array $env The environment vars to add
     * @param bool $overwriteOld Whether or not to overwrite all old environment vars
     * @return $this For method chaining
     */
    public function withEnvironmentVars(array $env, $overwriteOld = false)
    {
        $this->addValuesToCollection($env, $this->env, $overwriteOld);

        return $this;
    }

    /**
     * Adds files to the response
     *
     * @param UploadedFile[] $files The files to upload
     * @param bool $overwriteOld Whether or not to overwrite all old files
     * @return $this For method chaining
     */
    public function withFiles(array $files, $overwriteOld = false)
    {
        $this->addValuesToCollection($files, $this->files, $overwriteOld);

        return $this;
    }

    /**
     * Adds headers to the response
     *
     * @param array $headers The headers to add
     * @param bool $overwriteOld Whether or not to overwrite all old headers
     * @return $this For method chaining
     */
    public function withHeaders(array $headers, $overwriteOld = false)
    {
        $prefixedServerVars = [];

        foreach ($headers as $name => $value) {
            $name = strtoupper($name);
            if (strpos($name, "HTTP_") === 0) {
                $prefixedServerVars[$name] = $value;
            } else {
                $prefixedServerVars["HTTP_$name"] = $value;
            }
        }

        $this->addValuesToCollection($prefixedServerVars, $this->server, $overwriteOld);

        return $this;
    }

    /**
     * Adds JSON to the response
     *
     * @param array $json The JSON to add
     * @return $this For method chaining
     */
    public function withJson(array $json)
    {
        $encodedJson = json_encode($json);
        $headers = [
            "CONTENT_TYPE" => "application/json",
            "CONTENT_LENGTH" => mb_strlen($encodedJson, "8bit")
        ];
        $this->withRawBody($encodedJson);
        $this->addValuesToCollection($headers, $this->server, false);

        return $this;
    }

    /**
     * Adds parameters to the response
     * The parameters are bound to the method type, eg if this is a POST request, these parameters are bound
     * to Request::getPost()
     *
     * @param array $parameters The parameters to add
     * @param bool $overwriteOld Whether or not to overwrite all old parameters
     * @return $this For method chaining
     */
    public function withParameters(array $parameters, $overwriteOld = false)
    {
        $this->addValuesToCollection($parameters, $this->parameters, $overwriteOld);

        return $this;
    }

    /**
     * Sets the raw body of the request
     *
     * @param mixed $rawBody The raw body
     * @return $this For method chaining
     */
    public function withRawBody($rawBody)
    {
        $this->rawBody = $rawBody;

        return $this;
    }

    /**
     * Adds server vars to the response
     *
     * @param array $serverVars The server vars to add
     * @param bool $overwriteOld Whether or not to overwrite all old server vars
     * @return $this For method chaining
     */
    public function withServerVars(array $serverVars, $overwriteOld = false)
    {
        $this->addValuesToCollection($serverVars, $this->server, $overwriteOld);

        return $this;
    }

    /**
     * Adds values to a collection
     *
     * @param array $values The values to add
     * @param array $collection The collection to add to
     * @param bool $overwriteOld Whether or not clear the collection before adding the new values
     */
    private function addValuesToCollection(array $values, array &$collection, $overwriteOld)
    {
        if ($overwriteOld) {
            $collection = [];
        }

        $collection = array_merge($collection, $values);
    }

    /**
     * Validates the properties to make sure a request object can be built
     *
     * @throws InvalidArgumentException Thrown if the properties were not set properly
     */
    private function validate()
    {
        if ($this->method === null) {
            throw new InvalidArgumentException("Method not set in request builder");
        }

        if ($this->url === null) {
            throw new InvalidArgumentException("URL not set in request builder");
        }
    }
}
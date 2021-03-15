<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens;

use InvalidArgumentException;

/**
 * Defines a JWT header
 */
class JwtHeader
{
    /** @var array The list of valid algorithms */
    private static $validAlgorithms = [
        'none',
        'HS256',
        'HS348',
        'HS512',
        'RS256',
        'RS384',
        'RS512'
    ];
    /** @var array The extra headers */
    private $headers = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    /**
     * @param string $algorithm The algorithm
     * @param array $headers The headers
     */
    public function __construct(string $algorithm = 'HS256', array $headers = [])
    {
        $this->setAlgorithm($algorithm);

        foreach ($headers as $name => $value) {
            $this->add($name, $value);
        }
    }

    /**
     * Base 64 encodes data for use in URLs
     *
     * @param string $data The data to encode
     * @return string The base 64 encoded data that's safe for URLs
     * @link http://php.net/manual/en/function.base64-encode.php#103849
     */
    private static function base64UrlEncode(string $data) : string
    {
        return \rtrim(\strtr(\base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Adds a header
     *
     * @param string $name The name of the header to add
     * @param mixed $value The value to add
     */
    public function add(string $name, $value)
    {
        switch ($name) {
            case 'alg':
                $this->setAlgorithm($value);
                break;
            default:
                $this->headers[$name] = $value;
                break;
        }
    }

    /**
     * Gets the header as a base64 URL-encoded string
     *
     * @return string The base64 URL-encoded string
     */
    public function encode() : string
    {
        return self::base64UrlEncode(\json_encode($this->getAll()));
    }

    /**
     * Gets the value for a header
     *
     * @param string $name The name of the header to get
     * @return mixed|null The value of the header if it exists, otherwise null
     */
    public function get(string $name)
    {
        if (!array_key_exists($name, $this->headers)) {
            return null;
        }

        return $this->headers[$name];
    }

    /**
     * @return string
     */
    public function getAlgorithm() : string
    {
        return $this->headers['alg'];
    }

    /**
     * Gets all the header values
     *
     * @return array All the header values
     */
    public function getAll() : array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->headers['cty'];
    }

    /**
     * @return string
     */
    public function getTokenType() : string
    {
        return $this->headers['typ'];
    }

    /**
     * @param string $algorithm
     * @throws InvalidArgumentException Thrown if the algorithm is not supported
     */
    private function setAlgorithm(string $algorithm)
    {
        if (!in_array($algorithm, self::$validAlgorithms)) {
            throw new InvalidArgumentException("Algorithm \"$algorithm\" is not supported");
        }

        $this->headers['alg'] = $algorithm;
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Requests;

use Opulence\Http\Headers;

/**
 * Defines the request headers
 */
class RequestHeaders extends Headers
{
    /** The client's host */
    const CLIENT_HOST = 'client-host';
    /** The client's IP address */
    const CLIENT_IP = 'client-ip';
    /** The client's port */
    const CLIENT_PORT = 'client-port';
    /** The client's protocol */
    const CLIENT_PROTO = 'client-proto';
    /** The forwarded headers */
    const FORWARDED = 'forwarded';
    /** @var array The list of HTTP request headers that don't begin with "HTTP_" */
    protected static $specialCaseHeaders = [
        'AUTH_TYPE' => true,
        'CONTENT_LENGTH' => true,
        'CONTENT_TYPE' => true,
        'PHP_AUTH_DIGEST' => true,
        'PHP_AUTH_PW' => true,
        'PHP_AUTH_TYPE' => true,
        'PHP_AUTH_USER' => true
    ];

    /**
     * @param array $values The mapping of header names to values
     */
    public function __construct(array $values = [])
    {
        // Only add "HTTP_" server values or special case values
        foreach ($values as $name => $value) {
            $name = strtoupper($name);

            if (isset(self::$specialCaseHeaders[$name]) || strpos($name, 'HTTP_') === 0) {
                $this->set($name, $value);
            }
        }

        parent::__construct();
    }

    /**
     * Removes the "http-" from the name
     *
     * @param string $name The name to normalize
     * @return string The normalized name
     */
    protected function normalizeName(string $name) : string
    {
        $name = parent::normalizeName($name);

        if (strpos($name, 'http-') === 0) {
            $name = substr($name, 5);
        }

        return $name;
    }
}

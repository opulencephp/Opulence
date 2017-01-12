<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Requests;

/**
 * Defines the request headers
 */
class RequestHeaders
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
}

<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Requests;

/**
 * Defines the list of request methods
 */
class RequestMethods
{
    /** The delete method */
    const DELETE = 'DELETE';
    /** The get method */
    const GET = 'GET';
    /** The post method */
    const POST = 'POST';
    /** The put method */
    const PUT = 'PUT';
    /** The head method */
    const HEAD = 'HEAD';
    /** The trace method */
    const TRACE = 'TRACE';
    /** The purge method */
    const PURGE = 'PURGE';
    /** The connect method */
    const CONNECT = 'CONNECT';
    /** The patch method */
    const PATCH = 'PATCH';
    /** The options method */
    const OPTIONS = 'OPTIONS';
}

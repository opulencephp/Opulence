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
    public const DELETE = 'DELETE';
    /** The get method */
    public const GET = 'GET';
    /** The post method */
    public const POST = 'POST';
    /** The put method */
    public const PUT = 'PUT';
    /** The head method */
    public const HEAD = 'HEAD';
    /** The trace method */
    public const TRACE = 'TRACE';
    /** The purge method */
    public const PURGE = 'PURGE';
    /** The connect method */
    public const CONNECT = 'CONNECT';
    /** The patch method */
    public const PATCH = 'PATCH';
    /** The options method */
    public const OPTIONS = 'OPTIONS';
}

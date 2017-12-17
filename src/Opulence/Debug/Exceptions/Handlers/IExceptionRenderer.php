<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Debug\Exceptions\Handlers;

use Exception;
use Throwable;

/**
 * Defines the interface for exception renderers to implement
 */
interface IExceptionRenderer
{
    /**
     * Renders an exception
     *
     * @param Throwable|Exception $ex The thrown exception
     */
    public function render($ex) : void;
}

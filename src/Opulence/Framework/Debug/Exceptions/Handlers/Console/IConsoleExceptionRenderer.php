<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Debug\Exceptions\Handlers\Console;

use Opulence\Console\Responses\IResponse;
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer;

/**
 * Defines the interface for console exception renderers to implement
 */
interface IConsoleExceptionRenderer extends IExceptionRenderer
{
    /**
     * Sets the response to render to
     *
     * @param IResponse $response The response to render to
     */
    public function setResponse(IResponse $response);
}
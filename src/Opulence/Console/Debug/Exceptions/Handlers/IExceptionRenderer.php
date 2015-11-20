<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Debug\Exceptions\Handlers;

use Opulence\Console\Responses\IResponse;
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer as IBaseRenderer;

/**
 * Defines the interface for console exception renderers to implement
 */
interface IExceptionRenderer extends IBaseRenderer
{
    /**
     * Sets the response to render to
     *
     * @param IResponse $response The response to render to
     */
    public function setResponse(IResponse $response);
}
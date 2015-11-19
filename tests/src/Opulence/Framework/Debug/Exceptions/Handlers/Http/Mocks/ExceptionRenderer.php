<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Debug\Exceptions\Hadnlers\Http\Mocks;

use Exception;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\ExceptionRenderer as BaseRenderer;

/**
 * Mocks the HTTP exception renderer for use in testing
 */
class ExceptionRenderer extends BaseRenderer
{
    /**
     * @inheritDoc
     */
    protected function getDevelopmentExceptionPage(Exception $ex)
    {
        return $ex->getMessage();
    }

    /**
     * @inheritDoc
     */
    protected function getProductionExceptionPage(Exception $ex)
    {
        return "Something went wrong";
    }
}
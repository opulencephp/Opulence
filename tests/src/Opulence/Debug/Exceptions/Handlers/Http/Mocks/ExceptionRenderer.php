<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Debug\Exceptions\Handlers\Http\Mocks;

use Exception;
use Opulence\Debug\Exceptions\Handlers\Http\ExceptionRenderer as BaseRenderer;

/**
 * Mocks the HTTP exception renderer for use in testing
 */
class ExceptionRenderer extends BaseRenderer
{
    /**
     * @inheritDoc
     */
    protected function getDevelopmentEnvironmentContent(Exception $ex)
    {
        return $ex->getMessage();
    }

    /**
     * @inheritDoc
     */
    protected function getProductionEnvironmentContent(Exception $ex)
    {
        return "Something went wrong";
    }
}
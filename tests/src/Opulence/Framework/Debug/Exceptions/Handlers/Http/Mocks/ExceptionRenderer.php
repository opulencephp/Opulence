<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Debug\Exceptions\Handlers\Http\Mocks;

use Exception;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\ExceptionRenderer as BaseRenderer;

/**
 * Mocks the HTTP exception renderer for use in testing
 */
class ExceptionRenderer extends BaseRenderer
{
    /**
     * @inheritdoc
     */
    protected function getDevelopmentEnvironmentContent(Exception $ex, $statusCode)
    {
        return $ex->getMessage();
    }

    /**
     * @inheritdoc
     */
    protected function getProductionEnvironmentContent(Exception $ex, $statusCode)
    {
        return "Something went wrong";
    }
}
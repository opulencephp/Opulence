<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Debug\Exceptions\Handlers\Http\Mocks;

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
    protected function getDevelopmentEnvironmentContent(Exception $ex, int $statusCode) : string
    {
        return $ex->getMessage();
    }

    /**
     * @inheritdoc
     */
    protected function getProductionEnvironmentContent(Exception $ex, int $statusCode) : string
    {
        return 'Something went wrong';
    }
}

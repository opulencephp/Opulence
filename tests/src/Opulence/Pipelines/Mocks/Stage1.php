<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Pipelines\Mocks;

use Closure;

/**
 * Mocks a pipeline stage
 */
class Stage1
{
    /**
     * Runs the callback on the input
     *
     * @param mixed $input The input
     * @param Closure $next The next closure
     * @return string The result of the stage
     */
    public function run($input, Closure $next)
    {
        return $next($input . "1");
    }
}
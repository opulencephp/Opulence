<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Pipelines\Mocks;

/**
 * Mocks a pipeline stage
 */
class Stage1
{
    /**
     * Runs the callback on the input
     *
     * @param mixed $input The input
     * @param callable $next The next closure
     * @return string The result of the stage
     */
    public function run($input, callable $next)
    {
        return $next($input . '1');
    }
}

<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Composer\Mocks;

use Opulence\Framework\Composer\Executable as BaseExecutable;

/**
 * Mocks the Composer executable for use in testing
 */
class Executable extends BaseExecutable
{
    /**
     * @inheritdoc
     * @return string The command itself
     */
    protected function execute($command)
    {
        return $command;
    }
}
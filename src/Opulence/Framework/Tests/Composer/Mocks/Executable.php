<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Composer\Mocks;

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
    protected function execute(string $command) : string
    {
        return $command;
    }
}

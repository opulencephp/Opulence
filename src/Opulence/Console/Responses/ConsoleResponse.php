<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Responses;

use Opulence\Console\Responses\Compilers\ICompiler;

/**
 * Defines the console response
 */
class ConsoleResponse extends StreamResponse
{
    /**
     * @param ICompiler $compiler The response compiler to use
     */
    public function __construct(ICompiler $compiler)
    {
        parent::__construct(fopen('php://stdout', 'w'), $compiler);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->write(chr(27) . '[2J' . chr(27) . '[;H');
    }
}

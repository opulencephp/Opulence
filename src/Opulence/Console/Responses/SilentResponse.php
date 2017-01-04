<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses;

use Opulence\Console\Responses\Compilers\MockCompiler;

/**
 * Defines the silent response, which does not write anything
 */
class SilentResponse extends Response
{
    public function __construct()
    {
        parent::__construct(new MockCompiler());
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function write($messages)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function writeln($messages)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $message, bool $includeNewLine)
    {
        // Don't do anything
    }
}

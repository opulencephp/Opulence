<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses;

use Opulence\Console\Responses\Compilers\ICompiler;

/**
 * Defines a basic response
 */
abstract class Response implements IResponse
{
    /** @var ICompiler The response compiler to use */
    protected $compiler = null;

    /**
     * @param ICompiler $compiler The response compiler to use
     */
    public function __construct(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @inheritdoc
     */
    public function setStyled(bool $isStyled)
    {
        $this->compiler->setStyled($isStyled);
    }

    /**
     * @inheritdoc
     */
    public function write($messages)
    {
        foreach ((array)$messages as $message) {
            $this->doWrite($this->compiler->compile($message), false);
        }
    }

    /**
     * @inheritdoc
     */
    public function writeln($messages)
    {
        foreach ((array)$messages as $message) {
            $this->doWrite($this->compiler->compile($message), true);
        }
    }

    /**
     * Actually performs the writing
     *
     * @param string $message The message to write
     * @param bool $includeNewLine True if we are to include a new line character at the end of the message
     */
    abstract protected function doWrite(string $message, bool $includeNewLine);
}
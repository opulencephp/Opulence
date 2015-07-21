<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a sub-compiler for use in testing
 */
namespace Opulence\Tests\Views\Compilers\SubCompilers\Mocks;
use Opulence\Views\IFortuneView;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\SubCompilers\SubCompiler as BaseSubCompiler;

class SubCompiler extends BaseSubCompiler
{
    /** @var callable The callback to execute when compiling */
    private $callback = null;

    /**
     * {@inheritdoc}
     * @param callable $callback The callback to execute when compiling
     *      It must accept the view as the first parameter and the contents as the second
     */
    public function __construct(ICompiler $parentCompiler, callable $callback)
    {
        parent::__construct($parentCompiler);

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(IFortuneView $view, $content)
    {
        return call_user_func_array($this->callback, [$view, $content]);
    }
}
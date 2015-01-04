<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a sub-compiler for use in testing
 */
namespace RDev\Tests\Views\Compilers\SubCompilers\Mocks;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Compilers\SubCompilers;

class SubCompiler extends SubCompilers\SubCompiler
{
    /** @var callable The callback to execute when compiling */
    private $callback = null;

    /**
     * {@inheritdoc}
     * @param callable $callback The callback to execute when compiling
     *      It must accept the template as the first parameter and the contents as the second
     */
    public function __construct(Compilers\ICompiler $parentCompiler, callable $callback)
    {
        parent::__construct($parentCompiler);

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template, $content)
    {
        return call_user_func_array($this->callback, [$template, $content]);
    }
}
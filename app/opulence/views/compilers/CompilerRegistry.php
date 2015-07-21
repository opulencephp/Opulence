<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the compiler dispatcher
 */
namespace Opulence\Views\Compilers;
use InvalidArgumentException;
use Opulence\Views\IView;

class CompilerRegistry implements ICompilerRegistry
{
    /** @var ICompiler[] The mapping of view classes to compiler instances */
    protected $compilers = [];

    /**
     * @inheritDoc
     */
    public function get(IView $view)
    {
        $viewClass = get_class($view);

        if(!isset($this->compilers[$viewClass]))
        {
            throw new InvalidArgumentException("No compiler registered for view class $viewClass");
        }

        return $this->compilers[$viewClass];
    }

    /**
     * @inheritdoc
     */
    public function registerCompiler($viewClass, ICompiler $compiler)
    {
        $this->compilers[$viewClass] = $compiler;
    }
}
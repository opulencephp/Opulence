<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for compiling views
 */
namespace Opulence\Views\Compilers;
use Opulence\Views\IView;

class Compiler implements ICompiler
{
    /** @var ICompilerRegistry The compiler registry */
    protected $registry = null;

    /**
     * @param ICompilerRegistry $registry The compiler registry
     */
    public function __construct(ICompilerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function compile(IView $view, $contents = null)
    {
        if($contents === null)
        {
            $contents = $view->getContents();
        }

        return $this->registry->get($view)->compile($view, $contents);
    }
} 
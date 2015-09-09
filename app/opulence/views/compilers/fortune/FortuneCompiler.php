<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune compiler
 */
namespace Opulence\Views\Compilers\Fortune;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\PHP\PHPCompiler;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;

class FortuneCompiler extends PHPCompiler
{
    /** @var ITranspiler The transpiler that converts Fortune code to PHP code */
    protected $transpiler = null;
    /** @var ICompiler The main view compiler */
    protected $parentCompiler = null;
    /** @var IViewFactory The view factory */
    protected $viewFactory = null;

    /**
     * @param ITranspiler $transpiler The transpiler that converts Fortune code to PHP code
     * @param ICompiler $parentCompiler The main view compiler
     * @param IViewFactory $viewFactory The view factory
     */
    public function __construct(ITranspiler $transpiler, ICompiler $parentCompiler, IViewFactory $viewFactory)
    {
        $this->transpiler = $transpiler;
        $this->parentCompiler = $parentCompiler;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(IView $view)
    {
        // Set some variables that will be used by the transpiled code
        $view->setContents($this->transpiler->transpile($view));
        $view->setVars([
            "__opulenceView" => $view,
            "__opulenceViewCompiler" => $this->parentCompiler,
            "__opulenceFortuneTranspiler" => $this->transpiler,
            "__opulenceViewFactory" => $this->viewFactory
        ]);

        return parent::compile($view);
    }
}
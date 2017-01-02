<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Fortune;

use Opulence\Views\Compilers\Php\PhpCompiler;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;

/**
 * Defines the Fortune compiler
 */
class FortuneCompiler extends PhpCompiler
{
    /** @var ITranspiler The transpiler that converts Fortune code to PHP code */
    protected $transpiler = null;
    /** @var IViewFactory The view factory */
    protected $viewFactory = null;

    /**
     * @param ITranspiler $transpiler The transpiler that converts Fortune code to PHP code
     * @param IViewFactory $viewFactory The view factory
     */
    public function __construct(ITranspiler $transpiler, IViewFactory $viewFactory)
    {
        $this->transpiler = $transpiler;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(IView $view) : string
    {
        // Set some variables that will be used by the transpiled code
        $view->setContents($this->transpiler->transpile($view));
        $view->setVars([
            "__opulenceView" => $view,
            "__opulenceFortuneTranspiler" => $this->transpiler,
            "__opulenceViewFactory" => $this->viewFactory
        ]);

        return trim(parent::compile($view));
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a compiler for basic PHP views
 */
namespace Opulence\Views\Compilers\PHP;

use Exception;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;

class PHPCompiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile(IView $view)
    {
        $obStartLevel = ob_get_level();
        ob_start();
        extract($view->getVars());

        try {
            if (eval('?>' . $view->getContents()) === false) {
                throw new ViewCompilerException("Invalid PHP in view");
            }
        }catch (Exception $ex) {
            // Clean the output buffer
            while (ob_get_level() > $obStartLevel) {
                ob_end_clean();
            }

            throw new ViewCompilerException($ex->getMessage());
        }

        return ob_get_clean();
    }
}
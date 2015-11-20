<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers\Php;

use Exception;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;

/**
 * Defines a compiler for basic PHP views
 */
class PhpCompiler implements ICompiler
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
        } catch (Exception $ex) {
            // Clean the output buffer
            while (ob_get_level() > $obStartLevel) {
                ob_end_clean();
            }

            throw new ViewCompilerException("Failed to compile PHP view", 0, $ex);
        }

        return ob_get_clean();
    }
}
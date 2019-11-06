<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Php;

use Exception;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\IView;
use Throwable;

/**
 * Defines a compiler for basic PHP views
 */
class PhpCompiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile(IView $view): string
    {
        $obStartLevel = ob_get_level();
        ob_start();
        $vars = $view->getVars();
        extract($vars);

        try {
            if (eval('?>' . $view->getContents()) === false) {
                throw new ViewCompilerException($view);
            }
        } catch (Exception $ex) {
            $this->rethrowException($ex, $view, $obStartLevel);
        } catch (Throwable $ex) {
            $this->rethrowException($ex, $view, $obStartLevel);
        }

        return ob_get_clean();
    }

    /**
     * Handles any exception thrown during compilation
     *
     * @param Exception|Throwable $ex The exception to handle
     * @param IView $view The view that caused the exception
     * @param int $obStartLevel The starting output buffer level
     * @throws ViewCompilerException The rethrown exception
     */
    protected function rethrowException($ex, IView $view, int $obStartLevel): void
    {
        // Clean the output buffer
        while (ob_get_level() > $obStartLevel) {
            ob_end_clean();
        }

        throw new ViewCompilerException($view, 0, $ex);
    }
}

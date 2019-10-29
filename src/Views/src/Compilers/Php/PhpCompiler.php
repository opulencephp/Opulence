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
                throw new ViewCompilerException('Invalid PHP in view');
            }
        } catch (Exception $ex) {
            $this->handleException($ex, $obStartLevel);
        } catch (Throwable $ex) {
            $this->handleException($ex, $obStartLevel);
        }

        return ob_get_clean();
    }

    /**
     * Handles any exception thrown during compilation
     *
     * @param Exception|Throwable $ex The exception to handle
     * @param int $obStartLevel The starting output buffer level
     * @throws Throwable|Exception Always rethrown
     */
    protected function handleException($ex, int $obStartLevel): void
    {
        // Clean the output buffer
        while (ob_get_level() > $obStartLevel) {
            ob_end_clean();
        }

        throw $ex;
    }
}

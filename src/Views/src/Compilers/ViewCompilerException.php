<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers;

use Exception;
use Opulence\Views\IView;
use Throwable;

/**
 * Defines a view compiler exception
 */
class ViewCompilerException extends Exception
{
    /**
     * @inheritdoc
     * @param IView $view The view that caused the exception
     */
    public function __construct(IView $view, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Exception thrown by view {$view->getPath()}", $code, $previous);
    }
}

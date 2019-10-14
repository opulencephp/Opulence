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

use Opulence\Views\IView;

/**
 * Defines methods for compiling views
 */
class Compiler implements ICompiler
{
    /** @var ICompilerRegistry The compiler registry */
    protected ICompilerRegistry $registry;

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
    public function compile(IView $view): string
    {
        return $this->registry->getCompiler($view)->compile($view);
    }
}

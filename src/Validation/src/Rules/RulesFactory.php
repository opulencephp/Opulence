<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Rules;

use Opulence\Validation\Rules\Errors\Compilers\Compiler;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;

/**
 * Defines the rules factory
 */
class RulesFactory
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected RuleExtensionRegistry $ruleExtensionRegistry;
    /** @var ErrorTemplateRegistry The error template registry */
    protected ErrorTemplateRegistry $errorTemplateRegistry;
    /** @var ICompiler The error template compiler */
    protected ICompiler $errorTemplateCompiler;

    /**
     * @param RuleExtensionRegistry|null $ruleExtensionRegistry The rule extension registry
     * @param ErrorTemplateRegistry|null $errorTemplateRegistry The error template registry
     * @param ICompiler|null $errorTemplateCompiler The error template compiler
     */
    public function __construct(
        RuleExtensionRegistry $ruleExtensionRegistry = null,
        ErrorTemplateRegistry $errorTemplateRegistry = null,
        ICompiler $errorTemplateCompiler = null
    ) {
        $this->ruleExtensionRegistry = $ruleExtensionRegistry ?? new RuleExtensionRegistry();
        $this->errorTemplateRegistry = $errorTemplateRegistry ?? new ErrorTemplateRegistry();
        $this->errorTemplateCompiler = $errorTemplateCompiler ?? new Compiler();
    }

    /**
     * Creates new rules
     *
     * @return Rules The new rules
     */
    public function createRules(): Rules
    {
        return new Rules($this->ruleExtensionRegistry, $this->errorTemplateRegistry, $this->errorTemplateCompiler);
    }
}

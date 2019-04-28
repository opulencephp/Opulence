<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Rules\Factories;

use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the rules factory
 */
class RulesFactory
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry;
    /** @var ErrorTemplateRegistry The error template registry */
    protected $errorTemplateRegistry;
    /** @var ICompiler The error template compiler */
    protected $errorTemplateCompiler;

    /**
     * @param RuleExtensionRegistry $ruleExtensionRegistry The rule extension registry
     * @param ErrorTemplateRegistry $errorTemplateRegistry The error template registry
     * @param ICompiler $errorTemplateCompiler The error template compiler
     */
    public function __construct(
        RuleExtensionRegistry $ruleExtensionRegistry,
        ErrorTemplateRegistry $errorTemplateRegistry,
        ICompiler $errorTemplateCompiler
    ) {
        $this->ruleExtensionRegistry = $ruleExtensionRegistry;
        $this->errorTemplateRegistry = $errorTemplateRegistry;
        $this->errorTemplateCompiler = $errorTemplateCompiler;
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

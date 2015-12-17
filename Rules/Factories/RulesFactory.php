<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules\Factories;

use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the rules factory
 */
class RulesFactory
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry = null;

    /**
     * @param RuleExtensionRegistry $ruleExtensionRegistry The rule extension registry
     */
    public function __construct(RuleExtensionRegistry $ruleExtensionRegistry)
    {
        $this->ruleExtensionRegistry = $ruleExtensionRegistry;
    }

    /**
     * Creates new rules
     *
     * @return Rules The new rules
     */
    public function createRules()
    {
        return new Rules($this->ruleExtensionRegistry);
    }
}
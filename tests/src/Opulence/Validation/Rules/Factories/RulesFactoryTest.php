<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules\Factories;

use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Tests the rules factory
 */
class RulesFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that rules are created
     */
    public function testRulesCreated()
    {
        /** @var RuleExtensionRegistry|\PHPUnit_Framework_MockObject_MockObject $ruleExtensionRegistry */
        $ruleExtensionRegistry = $this->getMock(RuleExtensionRegistry::class);
        /** @var ErrorTemplateRegistry|\PHPUnit_Framework_MockObject_MockObject $errorTemplateRegistry */
        $errorTemplateRegistry = $this->getMock(ErrorTemplateRegistry::class);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $errorTemplateCompiler */
        $errorTemplateCompiler = $this->getMock(ICompiler::class);
        $factory = new RulesFactory($ruleExtensionRegistry, $errorTemplateRegistry, $errorTemplateCompiler);
        $this->assertInstanceOf(Rules::class, $factory->createRules());
    }
}
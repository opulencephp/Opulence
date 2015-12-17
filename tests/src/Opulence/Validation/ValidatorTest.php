<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation;

use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Tests the validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Validator The validator to use in tests */
    private $validator = null;
    /** @var RulesFactory|\PHPUnit_Framework_MockObject_MockObject The rules factory */
    private $rulesFactory = null;
    /** @var RuleExtensionRegistry|\PHPUnit_Framework_MockObject_MockObject The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = $this->getMock(RuleExtensionRegistry::class);
        $this->rulesFactory = $this->getMock(RulesFactory::class, [], [$this->registry]);
        $this->validator = new Validator($this->rulesFactory, $this->registry);
    }

    /**
     * Tests that extensions can be registered
     */
    public function testExtensionsCanBeRegistered()
    {
        $callback = function () {
        };
        $this->registry->expects($this->once())
            ->method("registerRuleExtension")
            ->with("foo", $callback);
        $this->assertSame($this->validator, $this->validator->registerRuleExtension("foo", $callback));
    }

    /**
     * Tests that field returns rules
     */
    public function testFieldReturnsRules()
    {
        $rules = $this->getMock(Rules::class, [], [$this->registry]);
        $this->rulesFactory->expects($this->once())
            ->method("createRules")
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field("foo"));
    }

    /**
     * Tests that rule pass results are respected
     */
    public function testRulePassResultsAreRespected()
    {
        $rules = $this->getMock(Rules::class, [], [$this->registry]);
        $rules->expects($this->at(0))
            ->method("passes")
            ->with("foo", "bar", ["baz" => "blah"])
            ->willReturn(true);
        $rules->expects($this->at(1))
            ->method("passes")
            ->with("dave", "young", ["is" => "awesome"])
            ->willReturn(false);
        $this->rulesFactory->expects($this->exactly(2))
            ->method("createRules")
            ->willReturn($rules);
        $this->assertTrue(
            $this->validator->field("foo")
                ->passes("foo", "bar", ["baz" => "blah"])
        );
        $this->assertFalse(
            $this->validator->field("bar")
                ->passes("dave", "young", ["is" => "awesome"])
        );
    }

    /**
     * Tests that the same rules are returned when specifying same field
     */
    public function testSameRulesAreReturnedWhenSpecifyingSameField()
    {
        $rules = $this->getMock(Rules::class, [], [$this->registry]);
        $this->rulesFactory->expects($this->once())
            ->method("createRules")
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field("foo"));
        $this->assertSame($rules, $this->validator->field("foo"));
    }
}
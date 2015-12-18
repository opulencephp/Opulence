<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation;

use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
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
    private $ruleExtensionRegistry = null;
    /** @var ErrorTemplateRegistry|\PHPUnit_Framework_MockObject_MockObject */
    private $errorTemplateRegistry;
    /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject */
    private $errorTemplateCompiler;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->ruleExtensionRegistry = $this->getMock(RuleExtensionRegistry::class);
        /** @var ErrorTemplateRegistry|\PHPUnit_Framework_MockObject_MockObject $errorTemplateRegistry */
        $this->errorTemplateRegistry = $this->getMock(ErrorTemplateRegistry::class);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $errorTemplateCompiler */
        $this->errorTemplateCompiler = $this->getMock(ICompiler::class);
        $this->rulesFactory = $this->getMock(
            RulesFactory::class,
            [],
            [$this->ruleExtensionRegistry, $this->errorTemplateRegistry, $this->errorTemplateCompiler]
        );
        $this->validator = new Validator($this->rulesFactory, $this->ruleExtensionRegistry);
    }

    /**
     * Tests that the errors are empty before running the validator
     */
    public function testErrorsAreEmptyBeforeRunningValidator()
    {
        $errors = $this->validator->getErrors();
        $this->assertInstanceOf(ErrorCollection::class, $errors);
        $this->assertEquals([], $errors->getAll());
    }

    /**
     * Tests that errors are reset when validating twice
     */
    public function testErrorsAreResetWhenValidatingTwice()
    {
        $rules = $this->getRules();
        $rules->expects($this->exactly(2))
            ->method("pass")
            ->willReturn(false);
        $rules->expects($this->exactly(2))
            ->method("getErrors")
            ->with("foo")
            ->willReturn(["error 1", "error 2"]);
        $this->rulesFactory->expects($this->once())
            ->method("createRules")
            ->willReturn($rules);
        $this->validator->field("foo");
        $this->assertFalse($this->validator->isValid(["foo" => "bar"]));
        $this->assertEquals(["foo" => ["error 1", "error 2"]], $this->validator->getErrors()->getAll());
        $this->assertFalse($this->validator->isValid(["foo" => "bar"]));
        $this->assertEquals(["foo" => ["error 1", "error 2"]], $this->validator->getErrors()->getAll());
    }

    /**
     * Tests that extensions can be registered
     */
    public function testExtensionsCanBeRegistered()
    {
        $callback = function () {
        };
        $this->ruleExtensionRegistry->expects($this->once())
            ->method("registerRuleExtension")
            ->with($callback, "foo");
        $this->assertSame($this->validator, $this->validator->registerRule($callback, "foo"));
    }

    /**
     * Tests that field returns rules
     */
    public function testFieldReturnsRules()
    {
        $rules = $this->getRules();
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
        $rules = $this->getRules();
        $rules->expects($this->at(0))
            ->method("pass")
            ->with("bar", ["baz" => "blah"])
            ->willReturn(true);
        $rules->expects($this->at(1))
            ->method("pass")
            ->with("dave", ["is" => "awesome"])
            ->willReturn(false);
        $this->rulesFactory->expects($this->exactly(2))
            ->method("createRules")
            ->willReturn($rules);
        $this->assertTrue(
            $this->validator->field("foo")
                ->pass("bar", ["baz" => "blah"])
        );
        $this->assertFalse(
            $this->validator->field("bar")
                ->pass("dave", ["is" => "awesome"])
        );
    }

    /**
     * Tests that the same rules are returned when specifying same field
     */
    public function testSameRulesAreReturnedWhenSpecifyingSameField()
    {
        $rules = $this->getRules();
        $this->rulesFactory->expects($this->once())
            ->method("createRules")
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field("foo"));
        $this->assertSame($rules, $this->validator->field("foo"));
    }

    /**
     * Gets mock rules
     *
     * @return Rules|\PHPUnit_Framework_MockObject_MockObject The rules
     */
    private function getRules()
    {
        return $this->getMock(
            Rules::class,
            [],
            [$this->ruleExtensionRegistry, $this->errorTemplateRegistry, $this->errorTemplateCompiler]
        );
    }
}
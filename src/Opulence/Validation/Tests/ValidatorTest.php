<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests;

use Opulence\Validation\Rules\BetweenRule;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;
use Opulence\Validation\Validator;

/**
 * Tests the validator
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
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
        $this->ruleExtensionRegistry = $this->createMock(RuleExtensionRegistry::class);
        /** @var ErrorTemplateRegistry|\PHPUnit_Framework_MockObject_MockObject $errorTemplateRegistry */
        $this->errorTemplateRegistry = $this->createMock(ErrorTemplateRegistry::class);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $errorTemplateCompiler */
        $this->errorTemplateCompiler = $this->createMock(ICompiler::class);
        $this->rulesFactory = $this->getMockBuilder(RulesFactory::class)
            ->setConstructorArgs([
                $this->ruleExtensionRegistry,
                $this->errorTemplateRegistry,
                $this->errorTemplateCompiler
            ])
            ->getMock();
        $this->validator = new Validator($this->rulesFactory);
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
            ->method('pass')
            ->willReturn(false);
        $rules->expects($this->exactly(2))
            ->method('getErrors')
            ->with('foo')
            ->willReturn(['error 1', 'error 2']);
        $this->rulesFactory->expects($this->once())
            ->method('createRules')
            ->willReturn($rules);
        $this->validator->field('foo');
        $this->assertFalse($this->validator->isValid(['foo' => 'bar']));
        $this->assertEquals(['foo' => ['error 1', 'error 2']], $this->validator->getErrors()->getAll());
        $this->assertFalse($this->validator->isValid(['foo' => 'bar']));
        $this->assertEquals(['foo' => ['error 1', 'error 2']], $this->validator->getErrors()->getAll());
    }

    /**
     * Tests that field returns rules
     */
    public function testFieldReturnsRules()
    {
        $rules = $this->getRules();
        $this->rulesFactory->expects($this->once())
            ->method('createRules')
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field('foo'));
    }

    /**
     * Tests that rule pass results are respected
     */
    public function testRulePassResultsAreRespected()
    {
        $rules = $this->getRules();
        $rules->expects($this->at(0))
            ->method('pass')
            ->with('bar', ['baz' => 'blah'])
            ->willReturn(true);
        $rules->expects($this->at(1))
            ->method('pass')
            ->with('dave', ['is' => 'awesome'])
            ->willReturn(false);
        $this->rulesFactory->expects($this->exactly(2))
            ->method('createRules')
            ->willReturn($rules);
        $this->assertTrue(
            $this->validator->field('foo')
                ->pass('bar', ['baz' => 'blah'])
        );
        $this->assertFalse(
            $this->validator->field('bar')
                ->pass('dave', ['is' => 'awesome'])
        );
    }

    /**
     * Tests that the same rules are returned when specifying same field
     */
    public function testSameRulesAreReturnedWhenSpecifyingSameField()
    {
        $rules = $this->getRules();
        $this->rulesFactory->expects($this->once())
            ->method('createRules')
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field('foo'));
        $this->assertSame($rules, $this->validator->field('foo'));
    }

    /**
     * Gets mock rules
     *
     * @return Rules|\PHPUnit_Framework_MockObject_MockObject The rules
     */
    private function getRules()
    {
        return $this->getMockBuilder(Rules::class)
            ->setConstructorArgs([
                $this->ruleExtensionRegistry,
                $this->errorTemplateRegistry,
                $this->errorTemplateCompiler
            ])
            ->getMock();
    }

    /**
     * Test using custom rules twice does not change their arguments
     */
    public function testUsingCustomRulesTwiceDoesNotChangeThem()
    {
        $customRule = new class extends BetweenRule {
            public function getSlug(): string
            {
                return 'customRule';
            }
        };

        $ruleExtensionRegistry = new RuleExtensionRegistry();
        $ruleExtensionRegistry->registerRuleExtension($customRule, 'customRule');
        $rulesFactory = new RulesFactory(
            $ruleExtensionRegistry,
            $this->errorTemplateRegistry,
            $this->errorTemplateCompiler
        );

        $validator = new Validator($rulesFactory);
        $validator->field('field1')->customRule(0, 5);
        $validator->field('field2')->customRule(0, 20);

        $this->assertFalse($validator->isValid(['field1' => 6, 'field2' => 15]));
    }
}

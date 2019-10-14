<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\TestsTemp;

use LogicException;
use Opulence\Validation\Rules\BetweenRule;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;
use Opulence\Validation\Validator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the validator
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    private Validator $validator;
    /** @var RulesFactory|MockObject The rules factory */
    private RulesFactory $rulesFactory;
    /** @var RuleExtensionRegistry|MockObject The registry to use in tests */
    private RuleExtensionRegistry $ruleExtensionRegistry;
    /** @var ErrorTemplateRegistry|MockObject */
    private ErrorTemplateRegistry $errorTemplateRegistry;
    /** @var ICompiler|MockObject */
    private ICompiler $errorTemplateCompiler;

    protected function setUp(): void
    {
        $this->ruleExtensionRegistry = $this->createMock(RuleExtensionRegistry::class);
        /** @var ErrorTemplateRegistry|MockObject $errorTemplateRegistry */
        $this->errorTemplateRegistry = $this->createMock(ErrorTemplateRegistry::class);
        /** @var ICompiler|MockObject $errorTemplateCompiler */
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

    public function testErrorsAreEmptyBeforeRunningValidator(): void
    {
        $errors = $this->validator->getErrors();
        $this->assertInstanceOf(ErrorCollection::class, $errors);
        $this->assertEquals([], $errors->getAll());
    }

    public function testErrorsAreResetWhenValidatingTwice(): void
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

    public function testFieldReturnsRules(): void
    {
        $rules = $this->getRules();
        $this->rulesFactory->expects($this->once())
            ->method('createRules')
            ->willReturn($rules);
        $this->assertSame($rules, $this->validator->field('foo'));
    }

    public function testRulePassResultsAreRespected(): void
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

    public function testSameRulesAreReturnedWhenSpecifyingSameField(): void
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
     * @return Rules|MockObject The rules
     */
    private function getRules(): Rules
    {
        return $this->getMockBuilder(Rules::class)
            ->setConstructorArgs([
                $this->ruleExtensionRegistry,
                $this->errorTemplateRegistry,
                $this->errorTemplateCompiler
            ])
            ->getMock();
    }
}

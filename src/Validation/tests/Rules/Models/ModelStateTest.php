<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Models;

use Opulence\Validation\IValidatorFactory;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\Rules;
use Opulence\Validation\Tests\Rules\Models\Mocks\User;
use Opulence\Validation\Tests\Rules\Models\Mocks\UserModelState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the model state
 */
class ModelStateTest extends TestCase
{
    /** @var IValidatorFactory|MockObject The validator factory */
    private IValidatorFactory $validatorFactory;
    /** @var IValidator|MockObject The validator */
    private IValidator $validator;
    /** @var Rules|MockObject The rules to use in tests */
    private Rules $rules;

    protected function setUp(): void
    {
        $this->rules = $this->getMockBuilder(Rules::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = $this->createMock(IValidator::class);
        $this->validator->expects($this->any())
            ->method('field')
            ->willReturn($this->rules);
        $this->validatorFactory = $this->createMock(IValidatorFactory::class);
        $this->validatorFactory->expects($this->any())
            ->method('createValidator')
            ->willReturn($this->validator);
    }

    public function testInvalidModel(): void
    {
        $user = new User(1, 'Dave', 'foo');
        $this->validator->expects($this->at(0))
            ->method('field')
            ->with('id');
        $this->validator->expects($this->at(1))
            ->method('field')
            ->with('name');
        $this->validator->expects($this->at(2))
            ->method('field')
            ->with('email');
        $this->validator->expects($this->once())
            ->method('isValid')
            ->with([
                'id' => 1,
                'name' => 'Dave',
                'email' => 'foo'
            ])
            ->willReturn(false);
        $errorCollection = $this->getMockBuilder(ErrorCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator->expects($this->once())
            ->method('getErrors')
            ->willReturn($errorCollection);
        $modelState = new UserModelState($user, $this->validatorFactory);
        $this->assertFalse($modelState->isValid());
        $this->assertInstanceOf(ErrorCollection::class, $modelState->getErrors());
    }

    public function testValidModel(): void
    {
        $user = new User(1, 'Dave', 'foo@bar.com');
        $this->validator->expects($this->at(0))
            ->method('field')
            ->with('id');
        $this->validator->expects($this->at(1))
            ->method('field')
            ->with('name');
        $this->validator->expects($this->at(2))
            ->method('field')
            ->with('email');
        $this->validator->expects($this->once())
            ->method('isValid')
            ->with([
                'id' => 1,
                'name' => 'Dave',
                'email' => 'foo@bar.com'
            ])
            ->willReturn(true);
        $errorCollection = $this->getMockBuilder(ErrorCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator->expects($this->once())
            ->method('getErrors')
            ->willReturn($errorCollection);
        $modelState = new UserModelState($user, $this->validatorFactory);
        $this->assertTrue($modelState->isValid());
        $this->assertInstanceOf(ErrorCollection::class, $modelState->getErrors());
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Tests\Factories;

use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Validator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the validator factory
 */
class ValidatorFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ValidatorFactory The factory to use in tests */
    private $validatorFactory = null;
    /** @var RulesFactory|MockObject The rules factory */
    private $rulesFactory = null;

    /**
     * Sets up the tests
     */
    protected function setUp() : void
    {
        $this->rulesFactory = $this->getMockBuilder(RulesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorFactory = new ValidatorFactory($this->rulesFactory);
    }

    /**
     * Tests that the validator is set up correctly
     */
    public function testValidatorIsSetUpCorrectly() : void
    {
        $this->assertEquals(
            new Validator($this->rulesFactory),
            $this->validatorFactory->createValidator()
        );
    }
}

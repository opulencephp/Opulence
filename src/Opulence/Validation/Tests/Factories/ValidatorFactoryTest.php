<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests\Factories;

use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Validator;

/**
 * Tests the validator factory
 */
class ValidatorFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ValidatorFactory The factory to use in tests */
    private $validatorFactory = null;
    /** @var RulesFactory|\PHPUnit_Framework_MockObject_MockObject The rules factory */
    private $rulesFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->rulesFactory = $this->getMockBuilder(RulesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorFactory = new ValidatorFactory($this->rulesFactory);
    }

    /**
     * Tests that the validator is set up correctly
     */
    public function testValidatorIsSetUpCorrectly()
    {
        $this->assertEquals(
            new Validator($this->rulesFactory),
            $this->validatorFactory->createValidator()
        );
    }
}

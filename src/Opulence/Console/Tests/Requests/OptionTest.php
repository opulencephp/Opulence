<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests;

use InvalidArgumentException;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;

/**
 * Tests the console option
 */
class OptionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Option The option to use in tests */
    private $option = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo option', 'bar');
    }

    /**
     * Tests checking whether or not the option value is an array
     */
    public function testCheckingIsValueArray() : void
    {
        $arrayOption = new Option('foo', 'f', OptionTypes::IS_ARRAY, 'Foo option');
        $this->assertTrue($arrayOption->valueIsArray());
    }

    /**
     * Tests checking whether or not the option value is optional
     */
    public function testCheckingIsValueOptional() : void
    {
        $requiredOption = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo option', 'bar');
        $optionalArgument = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo option', 'bar');
        $this->assertFalse($requiredOption->valueIsOptional());
        $this->assertTrue($optionalArgument->valueIsOptional());
    }

    /**
     * Tests checking whether or not the option value is permitted
     */
    public function testCheckingIsValuePermitted() : void
    {
        $requiredOption = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo option', 'bar');
        $notPermittedOption = new Option('foo', 'f', OptionTypes::NO_VALUE, 'Foo option', 'bar');
        $this->assertTrue($requiredOption->valueIsPermitted());
        $this->assertFalse($notPermittedOption->valueIsPermitted());
    }

    /**
     * Tests checking whether or not the option value is required
     */
    public function testCheckingIsValueRequired() : void
    {
        $requiredOption = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo option', 'bar');
        $optionalArgument = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo option', 'bar');
        $this->assertTrue($requiredOption->valueIsRequired());
        $this->assertFalse($optionalArgument->valueIsRequired());
    }

    /**
     * Tests getting the default value
     */
    public function testGettingDefaultValue() : void
    {
        $this->assertEquals('bar', $this->option->getDefaultValue());
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription() : void
    {
        $this->assertEquals('Foo option', $this->option->getDescription());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName() : void
    {
        $this->assertEquals('foo', $this->option->getName());
    }

    /**
     * Tests getting the short name
     */
    public function testGettingShortName() : void
    {
        $this->assertEquals('f', $this->option->getShortName());
    }

    /**
     * Tests specifying a short name that is not an alphabet character
     */
    public function testNonAlphabeticShortName() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Option('foo', '-', OptionTypes::REQUIRED_VALUE, 'Foo option', 'bar');
    }

    /**
     * Tests setting the type to both optional and no value
     */
    public function testSettingTypeToOptionalAndNoValue() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE | OptionTypes::NO_VALUE, 'Foo argument');
    }

    /**
     * Tests setting the type to both optional and required
     */
    public function testSettingTypeToOptionalAndRequired() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE | OptionTypes::REQUIRED_VALUE, 'Foo argument');
    }

    /**
     * Tests setting the type to both required and no value
     */
    public function testSettingTypeToRequiredAndNoValue() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Option('foo', 'f', OptionTypes::REQUIRED_VALUE | OptionTypes::NO_VALUE, 'Foo argument');
    }

    /**
     * Tests specifying a short name that is too long
     */
    public function testTooLongShortName() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Option('foo', 'foo', OptionTypes::REQUIRED_VALUE, 'Foo option', 'bar');
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests;

use InvalidArgumentException;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;

/**
 * Tests the console argument
 */
class ArgumentTest extends \PHPUnit\Framework\TestCase
{
    /** @var Argument The argument to use in tests */
    private $argument = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->argument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
    }

    /**
     * Tests checking whether or not the argument is an array
     */
    public function testCheckingIsArray()
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo argument', 'bar');
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
        $arrayArgument = new Argument('foo', ArgumentTypes::IS_ARRAY, 'Foo argument');
        $this->assertTrue($arrayArgument->isArray());
        $this->assertFalse($requiredArgument->isArray());
        $this->assertFalse($optionalArgument->isArray());
        $arrayArgument = new Argument('foo', ArgumentTypes::IS_ARRAY | ArgumentTypes::OPTIONAL, 'Foo argument');
        $this->assertTrue($arrayArgument->isArray());
        $arrayArgument = new Argument('foo', ArgumentTypes::IS_ARRAY | ArgumentTypes::REQUIRED, 'Foo argument');
        $this->assertTrue($arrayArgument->isArray());
    }

    /**
     * Tests checking whether or not the argument is optional
     */
    public function testCheckingIsOptional()
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo argument', 'bar');
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
        $optionalArrayArgument = new Argument('foo', ArgumentTypes::OPTIONAL | ArgumentTypes::IS_ARRAY, 'Foo argument');
        $this->assertFalse($requiredArgument->isOptional());
        $this->assertTrue($optionalArgument->isOptional());
        $this->assertTrue($optionalArrayArgument->isOptional());
    }

    /**
     * Tests checking whether or not the argument is required
     */
    public function testCheckingIsRequired()
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo argument', 'bar');
        $requiredArrayArgument = new Argument('foo', ArgumentTypes::REQUIRED | ArgumentTypes::IS_ARRAY, 'Foo argument');
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
        $this->assertTrue($requiredArgument->isRequired());
        $this->assertTrue($requiredArrayArgument->isRequired());
        $this->assertFalse($optionalArgument->isRequired());
    }

    /**
     * Tests getting the default value
     */
    public function testGettingDefaultValue()
    {
        $this->assertEquals('bar', $this->argument->getDefaultValue());
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription()
    {
        $this->assertEquals('Foo argument', $this->argument->getDescription());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals('foo', $this->argument->getName());
    }

    /**
     * Tests setting the type to both optional and required
     */
    public function testSettingTypeToOptionalAndRequired()
    {
        $this->expectException(InvalidArgumentException::class);
        new Argument('foo', ArgumentTypes::OPTIONAL | ArgumentTypes::REQUIRED, 'Foo argument');
    }
}

<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    private $argument;

    protected function setUp(): void
    {
        $this->argument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
    }

    public function testCheckingIsArray(): void
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

    public function testCheckingIsOptional(): void
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo argument', 'bar');
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
        $optionalArrayArgument = new Argument('foo', ArgumentTypes::OPTIONAL | ArgumentTypes::IS_ARRAY, 'Foo argument');
        $this->assertFalse($requiredArgument->isOptional());
        $this->assertTrue($optionalArgument->isOptional());
        $this->assertTrue($optionalArrayArgument->isOptional());
    }

    public function testCheckingIsRequired(): void
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo argument', 'bar');
        $requiredArrayArgument = new Argument('foo', ArgumentTypes::REQUIRED | ArgumentTypes::IS_ARRAY, 'Foo argument');
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo argument', 'bar');
        $this->assertTrue($requiredArgument->isRequired());
        $this->assertTrue($requiredArrayArgument->isRequired());
        $this->assertFalse($optionalArgument->isRequired());
    }

    public function testGettingDefaultValue(): void
    {
        $this->assertEquals('bar', $this->argument->getDefaultValue());
    }

    public function testGettingDescription(): void
    {
        $this->assertEquals('Foo argument', $this->argument->getDescription());
    }

    public function testGettingName(): void
    {
        $this->assertEquals('foo', $this->argument->getName());
    }

    public function testSettingTypeToOptionalAndRequired(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Argument('foo', ArgumentTypes::OPTIONAL | ArgumentTypes::REQUIRED, 'Foo argument');
    }
}

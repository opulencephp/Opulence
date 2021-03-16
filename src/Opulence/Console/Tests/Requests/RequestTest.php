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
use Opulence\Console\Requests\Request;

/**
 * Tests the console request
 */
class RequestTest extends \PHPUnit\Framework\TestCase
{
    /** @var Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->request = new Request();
    }

    /**
     * Tests adding multiple values for an option
     */
    public function testAddingMultipleValuesForOption()
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
        $this->request->addOptionValue('foo', 'baz');
        $this->assertEquals(['bar', 'baz'], $this->request->getOptionValue('foo'));
    }

    /**
     * Tests checking if an option with a value is set
     */
    public function testCheckingIfOptionWithValueIsSet()
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    /**
     * Tests checking if an option without a value is set
     */
    public function testCheckingIfOptionWithoutValueIsSet()
    {
        $this->request->addOptionValue('foo', null);
        $this->assertTrue($this->request->optionIsSet('foo'));
    }

    /**
     * Tests getting all arguments
     */
    public function testGettingAllArguments()
    {
        $this->request->addArgumentValue('foo');
        $this->request->addArgumentValue('bar');
        $this->assertEquals(['foo', 'bar'], $this->request->getArgumentValues());
    }

    /**
     * Tests getting all options
     */
    public function testGettingAllOptions()
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->request->addOptionValue('baz', 'blah');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $this->request->getOptionValues());
    }

    /**
     * Tests getting the command name
     */
    public function testGettingCommandName()
    {
        $this->request->setCommandName('foo');
        $this->assertEquals('foo', $this->request->getCommandName());
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->request->getOptionValue('foo');
    }

    /**
     * Tests getting an option
     */
    public function testGettingOption()
    {
        $this->request->addOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->request->getOptionValue('foo'));
    }
}

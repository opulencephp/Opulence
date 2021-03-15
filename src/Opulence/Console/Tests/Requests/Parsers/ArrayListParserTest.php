<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests\Parsers;

use InvalidArgumentException;
use Opulence\Console\Requests\Parsers\ArrayListParser;

/**
 * Tests the array list parser
 */
class ArrayListParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayListParser The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new ArrayListParser();
    }

    /**
     * Tests that backslashes are respected
     */
    public function testBackslashesAreRespected()
    {
        $request = $this->parser->parse([
            'name' => 'foo',
            'arguments' => ['bar\\baz']
        ]);
        $this->assertEquals(['bar\\baz'], $request->getArgumentValues());
    }

    /**
     * Test not passing arguments
     */
    public function testNotPassingArguments()
    {
        $request = $this->parser->parse([
            'name' => 'foo',
            'options' => ['--name=dave', '-r']
        ]);
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Test not passing options
     */
    public function testNotPassingOptions()
    {
        $request = $this->parser->parse([
            'name' => 'foo',
            'arguments' => ['bar']
        ]);
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing arguments and options
     */
    public function testParsingArgumentsAndOptions()
    {
        $request = $this->parser->parse([
            'name' => 'foo',
            'arguments' => ['bar'],
            'options' => ['--name=dave', '-r']
        ]);
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Test passing the command name
     */
    public function testPassingCommandName()
    {
        $request = $this->parser->parse([
            'name' => 'mycommand'
        ]);
        $this->assertEquals('mycommand', $request->getCommandName());
    }

    /**
     * Tests passing in an invalid input type
     */
    public function testPassingInvalidInputType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse('foo');
    }
}

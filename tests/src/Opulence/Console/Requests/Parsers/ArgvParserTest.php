<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests\Parsers;

use InvalidArgumentException;

/**
 * Tests the argv parser
 */
class ArgvParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgvParser The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new ArgvParser();
    }

    /**
     * Tests parsing arguments and options
     */
    public function testParsingArgumentsAndOptions()
    {
        $request = $this->parser->parse(["apex", "foo", "bar", "-r", "--name=dave"]);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing a null string
     */
    public function testParsingNullString()
    {
        $_SERVER["argv"] = ["apex", "foo", "bar", "-r", "--name=dave"];
        $request = $this->parser->parse(null);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing option with no value
     */
    public function testParsingOptionWithNoValue()
    {
        $request = $this->parser->parse(["apex", "foo", "--name"]);
        $this->assertNull($request->getOptionValue("name"));
    }

    /**
     * Tests passing in an invalid input type
     */
    public function testPassingInvalidInputType()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->parser->parse("foo");
    }
}
<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Requests\Parsers;

use InvalidArgumentException;
use Opulence\Console\Requests\Parsers\ArgvParser;

/**
 * Tests the argv parser
 */
class ArgvParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArgvParser The parser to use in tests */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new ArgvParser();
    }

    public function testBackslashesAreRespected(): void
    {
        $request = $this->parser->parse(['apex', 'foo', 'bar\\baz']);
        $this->assertEquals(['bar\\baz'], $request->getArgumentValues());
    }

    public function testParsingArgumentsAndOptions(): void
    {
        $request = $this->parser->parse(['apex', 'foo', 'bar', '-r', '--name=dave']);
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingNullString(): void
    {
        $_SERVER['argv'] = ['apex', 'foo', 'bar', '-r', '--name=dave'];
        $request = $this->parser->parse(null);
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingOptionWithNoValue(): void
    {
        $request = $this->parser->parse(['apex', 'foo', '--name']);
        $this->assertNull($request->getOptionValue('name'));
    }

    public function testPassingInvalidInputType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse('foo');
    }
}

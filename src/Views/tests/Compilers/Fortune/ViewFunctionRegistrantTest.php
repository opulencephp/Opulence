<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Compilers\Fortune;

use Opulence\Views\Caching\ICache;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;
use Opulence\Views\Compilers\Fortune\Transpiler;
use Opulence\Views\Filters\XssFilter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the Fortune view function registrant
 */
class ViewFunctionRegistrantTest extends \PHPUnit\Framework\TestCase
{
    /** @var Transpiler The transpiler to use in tests */
    private Transpiler $transpiler;

    protected function setUp(): void
    {
        $xssFilter = new XssFilter();
        /** @var ICache|MockObject $cache */
        $cache = $this->createMock(ICache::class);
        $cache->expects($this->any())
            ->method('has')
            ->willReturn(false);
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), $cache, $xssFilter);
    }

    public function testCSSFunction(): void
    {
        // Test a single value
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">',
            $this->transpiler->callViewFunction('css', 'foo')
        );

        // Test multiple values
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">' .
            "\n" .
            '<link href="bar" rel="stylesheet">',
            $this->transpiler->callViewFunction('css', ['foo', 'bar'])
        );
    }

    public function testCharsetFunction(): void
    {
        $charset = 'utf-8';
        $this->assertEquals(
            '<meta charset="' . $charset . '">',
            $this->transpiler->callViewFunction('charset', $charset)
        );
    }

    public function testFaviconFunction(): void
    {
        $path = 'foo';
        $this->assertEquals(
            '<link href="' . $path . '" rel="shortcut icon">',
            $this->transpiler->callViewFunction('favicon', $path)
        );
    }

    /**
     * Tests the http-equiv function
     */
    public function testHttpEquivFunction(): void
    {
        $name = 'refresh';
        $value = 30;
        $this->assertEquals(
            '<meta http-equiv="' . $name . '" content="' . $value . '">',
            $this->transpiler->callViewFunction('httpEquiv', $name, $value)
        );
    }

    public function testHttpMethodInput(): void
    {
        $httpMethod = 'PUT';
        $this->assertEquals(
            '<input type="hidden" name="_method" value="' . $httpMethod . '">',
            $this->transpiler->callViewFunction('httpMethodInput', $httpMethod)
        );
    }

    public function testMetaDescriptionFunction(): void
    {
        $metaDescription = 'A&W is a root beer';
        $this->assertEquals(
            '<meta name="description" content="' . htmlentities($metaDescription) . '">',
            $this->transpiler->callViewFunction('metaDescription', $metaDescription)
        );
    }

    public function testMetaKeywordsFunction(): void
    {
        $metaKeywords = ['A&W', 'root beer'];
        $this->assertEquals(
            '<meta name="keywords" content="' . implode(',', array_map('htmlentities', $metaKeywords)) . '">',
            $this->transpiler->callViewFunction('metaKeywords', $metaKeywords)
        );
    }

    public function testScriptFunction(): void
    {
        // Test a single value
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>',
            $this->transpiler->callViewFunction('script', 'foo')
        );

        // Test multiple values
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>' .
            PHP_EOL .
            '<script type="text/javascript" src="bar"></script>',
            $this->transpiler->callViewFunction('script', ['foo', 'bar'])
        );

        // Test a single value with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>',
            $this->transpiler->callViewFunction('script', 'foo', 'text/ecmascript')
        );

        // Test multiple values with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>' .
            PHP_EOL .
            '<script type="text/ecmascript" src="bar"></script>',
            $this->transpiler->callViewFunction('script', ['foo', 'bar'], 'text/ecmascript')
        );
    }

    public function testTitleFunction(): void
    {
        $title = 'A&W';
        $this->assertEquals(
            '<title>' . htmlentities($title) . '</title>',
            $this->transpiler->callViewFunction('pageTitle', $title)
        );
    }
}

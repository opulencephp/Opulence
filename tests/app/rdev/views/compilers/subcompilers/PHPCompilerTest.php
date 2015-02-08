<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PHP sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Tests\Mocks;
use RDev\Tests\Views\Compilers\Tests;
use RDev\Views;

class PHPCompilerTest extends Tests\Compiler
{
    /** @var TagCompiler The sub-compiler to test */
    private $subCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->subCompiler = new PHPCompiler($this->compiler, $this->xssFilter);
    }

    /**
     * Tests compiling an array variable inside tags
     */
    public function testCompilingArrayVariableInsideTags()
    {
        $delimiters = [
            [
                Views\Template::DEFAULT_OPEN_ESCAPED_TAG_DELIMITER,
                Views\Template::DEFAULT_CLOSE_ESCAPED_TAG_DELIMITER
            ],
            [
                Views\Template::DEFAULT_OPEN_UNESCAPED_TAG_DELIMITER,
                Views\Template::DEFAULT_CLOSE_UNESCAPED_TAG_DELIMITER
            ]
        ];
        $templateContents = '<?php foreach(["foo" => ["bar", "baz"]] as $v): ?>%s$v[1]%s<?php endforeach; ?>';
        $this->template->setContents(sprintf($templateContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertEquals("{{\"baz\"}}", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents(sprintf($templateContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("{{!\"baz\"!}}", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a function inside escaped tags
     */
    public function testCompilingFunctionInsideEscapedTags()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "A&W";
        });
        $this->template->setContents("{{foo()}}");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "A&amp;W",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests compiling a function inside unescaped tags
     */
    public function testCompilingFunctionInsideUnescapedTags()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "A&W";
        });
        $this->template->setContents("{{!foo()!}}");
        $this->assertEquals("A&W", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling invalid PHP
     */
    public function testCompilingInvalidPHP()
    {
        $this->setExpectedException("RDev\\Views\\Compilers\\ViewCompilerException");
        $contents = $this->fileSystem->read(__DIR__ . "/../../files/TestWithInvalidPHP.html");
        $this->template->setContents($contents);
        // Temporarily disable error reporting to prevent stuff from being printed in the error logs
        $originalErrorReporting = error_reporting();
        error_reporting(0);
        $this->subCompiler->compile($this->template, $this->template->getContents());
        error_reporting($originalErrorReporting);
    }

    /**
     * Tests compiling a template with PHP code
     */
    public function testCompilingTemplateWithPHPCode()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_PHP_CODE);
        $this->template->setContents($contents);
        $user1 = new Mocks\User(1, "foo");
        $user2 = new Mocks\User(2, "bar");
        $this->template->setTag("listDescription", "usernames");
        $this->template->setVar("users", [$user1, $user2]);
        $this->template->setVar("coolestGuy", "Dave");
        $functionResult = $this->registerFunction();
        $this->assertEquals(
            'TEST List of {{!listDescription!}} on ' . $functionResult . ':
<ul>
    <li>foo</li><li>bar</li></ul> 2 items
<br>Dave is a pretty cool guy. Alternative syntax works! I agree. Fake closing PHP tag: ?>',
            $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a variable inside tags
     */
    public function testCompilingVariableInsideTags()
    {
        $delimiters = [
            [
                Views\Template::DEFAULT_OPEN_ESCAPED_TAG_DELIMITER,
                Views\Template::DEFAULT_CLOSE_ESCAPED_TAG_DELIMITER
            ],
            [
                Views\Template::DEFAULT_OPEN_UNESCAPED_TAG_DELIMITER,
                Views\Template::DEFAULT_CLOSE_UNESCAPED_TAG_DELIMITER
            ]
        ];
        $templateContents = '<?php foreach(["foo"] as $v): ?>%s$v%s<?php endforeach; ?>';
        $this->template->setContents(sprintf($templateContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertEquals("{{\"foo\"}}", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents(sprintf($templateContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("{{!\"foo\"!}}", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with a function that spans multiple lines
     */
    public function testFunctionThatSpansMultipleLines()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return $input . "bar";
        });
        $this->template->setContents("{{
        foo(
        'foo'
        )
        }}");
        $this->assertEquals("foobar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with a function that has spaces between the open and close tags
     */
    public function testFunctionWithSpacesBetweenTags()
    {
        $this->template->setContents('{{! foo("bar") !}}');
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            echo $input;
        });
        $this->assertEquals("bar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with multiple calls to the same function
     */
    public function testMultipleCallsOfSameFunction()
    {
        $this->compiler->registerTemplateFunction("foo",
            function ($param1 = null, $param2 = null)
            {
                if($param1 == null && $param2 == null)
                {
                    return "No params";
                }
                elseif($param1 == null)
                {
                    return "Param 2 set";
                }
                elseif($param2 == null)
                {
                    return "Param 1 set";
                }
                else
                {
                    return "Both params set";
                }
            }
        );
        $this->template->setContents(
            '{{!foo()!}}, {{!foo()!}}, {{!foo("bar")!}}, {{!foo(null, "bar")!}}, {{!foo("bar", "blah")!}}'
        );
        $this->assertEquals(
            'No params, No params, Param 1 set, Param 2 set, Both params set',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }
}
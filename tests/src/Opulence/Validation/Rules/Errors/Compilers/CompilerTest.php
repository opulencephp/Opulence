<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules\Errors\Compilers;

/**
 * Tests the error template compiler
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
    }

    /**
     * Tests compiling a template with arg placeholders
     */
    public function testCompilingTemplateWithArgPlaceholders()
    {
        $this->assertEquals(
            "foo dave baz young",
            $this->compiler->compile(
                "foo",
                "foo :bar baz :blah",
                ["bar" => "dave", "blah" => "young"]
            )
        );
    }

    /**
     * Tests compiling a template with arg placeholders not in same order as args
     */
    public function testCompilingTemplateWithArgPlaceholdersNotInSameOrderAsArgs()
    {
        $this->assertEquals(
            "foo dave baz young",
            $this->compiler->compile(
                "foo",
                "foo :bar baz :blah",
                ["blah" => "young", "bar" => "dave"]
            )
        );
    }

    /**
     * Tests compiling a template with field and arg placeholders
     */
    public function testCompilingTemplateWithFieldAndArgPlaceholders()
    {
        $this->assertEquals(
            "foo the-field dave baz young",
            $this->compiler->compile(
                "the-field",
                "foo :field :bar baz :blah",
                ["bar" => "dave", "blah" => "young"]
            )
        );
    }

    /**
     * Tests compiling a template with a field placeholder
     */
    public function testCompilingTemplateWithFieldPlaceholder()
    {
        $this->assertEquals(
            "foo bar",
            $this->compiler->compile("foo", ":field bar")
        );
    }

    /**
     * Tests compiling a with leftover placeholders
     */
    public function testCompilingTemplateWithLeftoverPlaceholders()
    {
        $this->assertEquals(
            "foo dave",
            $this->compiler->compile("foo",
                "foo :bar :baz",
                ["bar" => "dave"]
            )
        );
    }

    /**
     * Tests compiling a template with no placeholders
     */
    public function testCompilingTemplateWithNoPlaceholders()
    {
        $this->assertEquals(
            "foo bar",
            $this->compiler->compile("foo", "foo bar")
        );
    }

    /**
     * Tests that an XSS attack is prevented
     */
    public function testXssAttackPrevented()
    {
        $this->assertEquals(
            filter_var("<script>A&W</script> <script>alert(123);</script>", FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            $this->compiler->compile(
                "<script>A&W</script>",
                ":field :foo",
                ["foo" => "<script>alert(123);</script>"]
            )
        );
    }
}
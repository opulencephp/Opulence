<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view
 */
namespace Opulence\Views;
use InvalidArgumentException;
use Opulence\Files\FileSystem;
use RuntimeException;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test view with default tags */
    const VIEW_PATH_WITH_DEFAULT_TAGS = "/files/TestWithDefaultTagDelimiters.html";
    /** The path to the test view with PHP code */
    const VIEW_PATH_WITH_INVALID_PHP_CODE = "/files/TestWithInvalidPHP.html";

    /** @var View The view to use in the tests */
    private $view = null;
    /** @var FileSystem The file system used to read views */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->view = new View();
        $this->fileSystem = new FileSystem();
    }

    /**
     * Tests getting the delimiters for a type that does not have any
     */
    public function testGettingDelimitersForTypeThatDoesNotHaveAny()
    {
        $this->assertEquals([null, null], $this->view->getDelimiters("foo"));
    }

    /**
     * Tests getting the directive delimiters
     */
    public function testGettingDirectiveDelimiters()
    {
        $directiveDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_DIRECTIVE);
        $this->assertEquals(View::DEFAULT_OPEN_DIRECTIVE_DELIMITER, $directiveDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_DIRECTIVE_DELIMITER, $directiveDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_DIRECTIVE, ["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $this->view->getDelimiters(View::DELIMITER_TYPE_DIRECTIVE));
    }

    /**
     * Tests getting an inherited part from the parent
     */
    public function testGettingInheritedPartFromParent()
    {
        $parent = clone $this->view;
        $parent->setPart("foo", "bar");
        $this->view->setParent($parent);
        $this->assertEquals("bar", $this->view->getPart("foo"));
        $this->assertEquals(["foo" => "bar"], $this->view->getParts());
    }

    /**
     * Tests getting an inherited tag from the parent
     */
    public function testGettingInheritedVarFromParent()
    {
        $parent = clone $this->view;
        $parent->setVar("foo", "bar");
        $this->view->setParent($parent);
        $this->assertEquals("bar", $this->view->getVar("foo"));
        $this->assertEquals(["foo" => "bar"], $this->view->getVars());
    }

    /**
     * Tests getting a non-existent parent
     */
    public function testGettingNonExistentParent()
    {
        $this->assertNull($this->view->getParent());
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertNull($this->view->getVar("foo"));
    }

    /**
     * Tests pushing a parent part
     */
    public function testGettingPartFromParent()
    {
        $parent = clone $this->view;
        $parent->setPart("foo", "bar");
        $this->view->setParent($parent);
        $this->assertEquals("bar", $this->view->getPart("foo"));
        $this->assertEquals(["foo" => "bar"], $this->view->getParts());
    }

    /**
     * Tests getting the sanitized tag delimiters
     */
    public function testGettingSanitizedTagDelimiters()
    {
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG, ["foo", "bar"]);
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals("foo", $sanitizedDelimiters[0]);
        $this->assertEquals("bar", $sanitizedDelimiters[1]);
    }

    /**
     * Tests getting the unsanitized tag delimiters
     */
    public function testGettingUnsanitizedTagDelimiters()
    {
        $unsanitizedTagDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG, ["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG));
    }

    /**
     * Tests getting a var
     */
    public function testGettingVar()
    {
        $this->view->setVar("foo", "bar");
        $this->assertEquals("bar", $this->view->getVar("foo"));
    }

    /**
     * Tests getting the vars
     */
    public function testGettingVars()
    {
        $this->view->setVar("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->view->getVars());
    }

    /**
     * Tests not setting the contents in the constructor
     */
    public function testNotSettingContentsInConstructor()
    {
        $this->assertEmpty($this->view->getContents());
    }

    /**
     * Tests setting the contents
     */
    public function testSettingContents()
    {
        $this->view->setContents("blah");
        $this->assertEquals("blah", $this->view->getContents());
    }

    /**
     * Tests setting the contents in the constructor
     */
    public function testSettingContentsInConstructor()
    {
        $view = new View("foo", "bar");
        $this->assertEquals("bar", $view->getContents());
    }

    /**
     * Tests setting the contents to a non-string
     */
    public function testSettingContentsToNonString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->view->setContents(["Not a string"]);
    }

    /**
     * Tests setting delimiters
     */
    public function testSettingDelimiters()
    {
        $this->view->setDelimiters("foo", ["bar", "baz"]);
        $this->assertEquals(["bar", "baz"], $this->view->getDelimiters("foo"));
    }

    /**
     * Tests setting multiple variables in a view
     */
    public function testSettingMultipleVariables()
    {
        $this->view->setVars(["foo" => "bar", "abc" => ["xyz"]]);
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(["foo" => "bar", "abc" => ["xyz"]], $vars);
    }

    /**
     * Tests setting the parent
     */
    public function testSettingParent()
    {
        $parent = clone $this->view;
        $this->view->setParent($parent);
        $this->assertSame($parent, $this->view->getParent());
    }

    /**
     * Tests setting a view part
     */
    public function testSettingPart()
    {
        $this->view->setPart("foo", "bar");
        $this->assertEquals("bar", $this->view->getPart("foo"));
    }

    /**
     * Tests setting the path in the constructor
     */
    public function testSettingPathInConstructor()
    {
        $view = new View("foo");
        $this->assertEquals("foo", $view->getPath());
    }

    /**
     * Tests setting the path in the setter
     */
    public function testSettingPathInSetter()
    {
        $this->view->setPath("foo");
        $this->assertEquals("foo", $this->view->getPath());
    }

    /**
     * Tests setting a variable in a view
     */
    public function testSettingSingleVariable()
    {
        $this->view->setVar("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(["foo" => "bar"], $vars);
    }

    /**
     * Tests that nothing is output from an invalid view
     */
    public function testThatNothingIsOutputFromInvalidView()
    {
        $output = "";
        $startOBLevel = ob_get_level();

        try
        {
            $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_INVALID_PHP_CODE);
            $this->view->setContents($contents);
        }
        catch(RuntimeException $ex)
        {
            // Don't do anything
        }
        finally
        {
            while(ob_get_level() > $startOBLevel)
            {
                $output .= ob_get_clean();
            }
        }

        $this->assertEmpty($output);
    }
}
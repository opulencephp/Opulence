<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests;

use Opulence\IO\FileSystem;
use Opulence\Views\View;
use RuntimeException;

/**
 * Tests the view
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    /** The path to the test view with default tags */
    const VIEW_PATH_WITH_DEFAULT_TAGS = '/files/TestWithDefaultTagDelimiters.html';
    /** The path to the test view with PHP code */
    const VIEW_PATH_WITH_INVALID_PHP_CODE = '/files/TestWithInvalidPhp.html';

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
     * Tests getting the comment delimiters
     */
    public function testGettingCommentDelimiters()
    {
        $directiveDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_COMMENT);
        $this->assertEquals(View::DEFAULT_OPEN_COMMENT_DELIMITER, $directiveDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_COMMENT_DELIMITER, $directiveDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_COMMENT, ['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->view->getDelimiters(View::DELIMITER_TYPE_COMMENT));
    }

    /**
     * Tests getting the delimiters for a type that does not have any
     */
    public function testGettingDelimitersForTypeThatDoesNotHaveAny()
    {
        $this->assertEquals([null, null], $this->view->getDelimiters('foo'));
    }

    /**
     * Tests getting the directive delimiters
     */
    public function testGettingDirectiveDelimiters()
    {
        $directiveDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_DIRECTIVE);
        $this->assertEquals(View::DEFAULT_OPEN_DIRECTIVE_DELIMITER, $directiveDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_DIRECTIVE_DELIMITER, $directiveDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_DIRECTIVE, ['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->view->getDelimiters(View::DELIMITER_TYPE_DIRECTIVE));
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertNull($this->view->getVar('foo'));
    }

    /**
     * Tests getting the sanitized tag delimiters
     */
    public function testGettingSanitizedTagDelimiters()
    {
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG, ['foo', 'bar']);
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals('foo', $sanitizedDelimiters[0]);
        $this->assertEquals('bar', $sanitizedDelimiters[1]);
    }

    /**
     * Tests getting the unsanitized tag delimiters
     */
    public function testGettingUnsanitizedTagDelimiters()
    {
        $unsanitizedTagDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG, ['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG));
    }

    /**
     * Tests getting a var
     */
    public function testGettingVar()
    {
        $this->view->setVar('foo', 'bar');
        $this->assertEquals('bar', $this->view->getVar('foo'));
    }

    /**
     * Tests getting the vars
     */
    public function testGettingVars()
    {
        $this->view->setVar('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->view->getVars());
    }

    /**
     * Tests checking if a view has a variable
     */
    public function testHasVar()
    {
        $this->assertFalse($this->view->hasVar('foo'));
        // Try a null value
        $this->view->setVar('bar', null);
        $this->assertTrue($this->view->hasVar('bar'));
        // Try a normal value
        $this->view->setVar('baz', 123);
        $this->assertTrue($this->view->hasVar('baz'));
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
        $this->view->setContents('blah');
        $this->assertEquals('blah', $this->view->getContents());
    }

    /**
     * Tests setting the contents in the constructor
     */
    public function testSettingContentsInConstructor()
    {
        $view = new View('foo', 'bar');
        $this->assertEquals('bar', $view->getContents());
    }

    /**
     * Tests setting delimiters
     */
    public function testSettingDelimiters()
    {
        $this->view->setDelimiters('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->view->getDelimiters('foo'));
    }

    /**
     * Tests setting multiple variables in a view
     */
    public function testSettingMultipleVariables()
    {
        $this->view->setVars(['foo' => 'bar', 'abc' => ['xyz']]);
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty('vars');
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(['foo' => 'bar', 'abc' => ['xyz']], $vars);
    }

    /**
     * Tests setting the path in the constructor
     */
    public function testSettingPathInConstructor()
    {
        $view = new View('foo');
        $this->assertEquals('foo', $view->getPath());
    }

    /**
     * Tests setting the path in the setter
     */
    public function testSettingPathInSetter()
    {
        $this->view->setPath('foo');
        $this->assertEquals('foo', $this->view->getPath());
    }

    /**
     * Tests setting a variable in a view
     */
    public function testSettingSingleVariable()
    {
        $this->view->setVar('foo', 'bar');
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty('vars');
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(['foo' => 'bar'], $vars);
    }

    /**
     * Tests that nothing is output from an invalid view
     */
    public function testThatNothingIsOutputFromInvalidView()
    {
        $output = '';
        $startOBLevel = ob_get_level();

        try {
            $contents = $this->fileSystem->read(__DIR__ . self::VIEW_PATH_WITH_INVALID_PHP_CODE);
            $this->view->setContents($contents);
        } catch (RuntimeException $ex) {
            // Don't do anything
        } finally {
            while (ob_get_level() > $startOBLevel) {
                $output .= ob_get_clean();
            }
        }

        $this->assertEmpty($output);
    }
}

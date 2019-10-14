<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\TestsTemp;

use Aphiria\IO\FileSystem;
use Opulence\Views\View;
use RuntimeException;

/**
 * Tests the view
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    /** The path to the test view with PHP code */
    private const VIEW_PATH_WITH_INVALID_PHP_CODE = '/files/TestWithInvalidPhp.html';

    private View $view;
    private FileSystem $fileSystem;

    protected function setUp(): void
    {
        $this->view = new View();
        $this->fileSystem = new FileSystem();
    }

    public function testGettingCommentDelimiters(): void
    {
        $directiveDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_COMMENT);
        $this->assertEquals(View::DEFAULT_OPEN_COMMENT_DELIMITER, $directiveDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_COMMENT_DELIMITER, $directiveDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_COMMENT, ['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->view->getDelimiters(View::DELIMITER_TYPE_COMMENT));
    }

    public function testGettingDelimitersForTypeThatDoesNotHaveAny(): void
    {
        $this->assertEquals([null, null], $this->view->getDelimiters('foo'));
    }

    public function testGettingDirectiveDelimiters(): void
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
    public function testGettingNonExistentVariable(): void
    {
        $this->assertNull($this->view->getVar('foo'));
    }

    public function testGettingSanitizedTagDelimiters(): void
    {
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG, ['foo', 'bar']);
        $sanitizedDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals('foo', $sanitizedDelimiters[0]);
        $this->assertEquals('bar', $sanitizedDelimiters[1]);
    }

    public function testGettingUnsanitizedTagDelimiters(): void
    {
        $unsanitizedTagDelimiters = $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG);
        $this->assertEquals(View::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[0]);
        $this->assertEquals(View::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[1]);
        $this->view->setDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG, ['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->view->getDelimiters(View::DELIMITER_TYPE_UNSANITIZED_TAG));
    }

    public function testGettingVar(): void
    {
        $this->view->setVar('foo', 'bar');
        $this->assertEquals('bar', $this->view->getVar('foo'));
    }

    public function testGettingVars(): void
    {
        $this->view->setVar('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->view->getVars());
    }

    public function testHasVar(): void
    {
        $this->assertFalse($this->view->hasVar('foo'));
        // Try a null value
        $this->view->setVar('bar', null);
        $this->assertTrue($this->view->hasVar('bar'));
        // Try a normal value
        $this->view->setVar('baz', 123);
        $this->assertTrue($this->view->hasVar('baz'));
    }

    public function testNotSettingContentsInConstructor(): void
    {
        $this->assertEmpty($this->view->getContents());
    }

    public function testSettingContents(): void
    {
        $this->view->setContents('blah');
        $this->assertEquals('blah', $this->view->getContents());
    }

    public function testSettingContentsInConstructor(): void
    {
        $view = new View('foo', 'bar');
        $this->assertEquals('bar', $view->getContents());
    }

    public function testSettingDelimiters(): void
    {
        $this->view->setDelimiters('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->view->getDelimiters('foo'));
    }

    public function testSettingMultipleVariables(): void
    {
        $this->view->setVars(['foo' => 'bar', 'abc' => ['xyz']]);
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty('vars');
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(['foo' => 'bar', 'abc' => ['xyz']], $vars);
    }

    public function testSettingPathInConstructor(): void
    {
        $view = new View('foo');
        $this->assertEquals('foo', $view->getPath());
    }

    public function testSettingPathInSetter(): void
    {
        $this->view->setPath('foo');
        $this->assertEquals('foo', $this->view->getPath());
    }

    public function testSettingSingleVariable(): void
    {
        $this->view->setVar('foo', 'bar');
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty('vars');
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(['foo' => 'bar'], $vars);
    }

    public function testThatNothingIsOutputFromInvalidView(): void
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

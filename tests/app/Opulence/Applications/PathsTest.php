<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications;

use InvalidArgumentException;

/**
 * Tests the framework paths
 */
class PathsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructing with an empty array
     */
    public function testConstructingWithEmptyArray()
    {
        $paths = new Paths([]);
        $this->assertNull($paths["foo"]);
    }

    /**
     * Tests that the real path is what's stored when passing path through constructor
     */
    public function testRealPathInConstructor()
    {
        $path = __DIR__ . "/../";
        $realPath = realpath($path);
        $paths = new Paths(["foo" => $path]);
        $this->assertEquals($realPath, $paths->offsetGet("foo"));
    }

    /**
     * Tests that the real path is what's stored when passing path through setter
     */
    public function testRealPathInSetter()
    {
        $path = __DIR__ . "/../";
        $realPath = realpath($path);
        $paths = new Paths([]);
        $paths->offsetSet("foo", $path);
        $this->assertEquals($realPath, $paths->offsetGet("foo"));
    }

    /**
     * Tests setting a null offset
     */
    public function testSettingNullOffset()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $paths = new Paths([]);
        $paths[] = "foo";
    }
}
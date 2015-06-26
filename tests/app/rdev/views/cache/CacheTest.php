<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the template cache
 */
namespace RDev\Views\Caching;
use RDev\Files\FileSystem;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileSystem The file system to use to read cached templates */
    private $fileSystem = null;
    /** @var Cache The cache to use in tests */
    private $cache = null;

    /**
     * Does some setup before any tests
     */
    public static function setUpBeforeClass()
    {
        if(!is_dir(__DIR__ . "/tmp"))
        {
            mkdir(__DIR__ . "/tmp");
        }
    }

    /**
     * Performs some garbage collection
     */
    public static function tearDownAfterClass()
    {
        $files = glob(__DIR__ . "/tmp/*");

        foreach($files as $file)
        {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        rmdir(__DIR__ . "/tmp");
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new FileSystem();
        $this->cache = new Cache($this->fileSystem, __DIR__ . "/tmp", 3600);
    }

    /**
     * Tests caching a template with a non-positive lifetime
     */
    public function testCachingWithNonPositiveLifetime()
    {
        $this->cache = new Cache($this->fileSystem, __DIR__ . "/tmp", 0);
        $this->cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertFalse($this->cache->has("foo", ["bar" => "baz"], ["blah" => "asdf"]));
        $this->assertNull($this->cache->get("foo", ["bar" => "baz"], ["blah" => "asdf"]));
    }

    /**
     * Tests checking for a template that does exist
     */
    public function testCheckingForExistingTemplate()
    {
        $this->cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertTrue($this->cache->has("foo", ["bar" => "baz"], ["blah" => "asdf"]));
        $this->assertEquals("compiled", $this->cache->get("foo", ["bar" => "baz"], ["blah" => "asdf"]));
    }

    /**
     * Tests checking for a template that exists but doesn't match on tags
     */
    public function testCheckingForExistingTemplateWithNoTagMatches()
    {
        $this->cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertFalse($this->cache->has("foo", ["bar" => "baz"], ["wrong" => "ahh"]));
    }

    /**
     * Tests checking for a template that exists but doesn't match on variables
     */
    public function testCheckingForExistingTemplateWithNoVariableMatches()
    {
        $this->cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertFalse($this->cache->has("foo", ["wrong" => "ahh"], ["blah" => "asdf"]));
    }

    /**
     * Tests checking for an expired template
     */
    public function testCheckingForExpiredTemplate()
    {
        // The negative expiration is a way of forcing everything to expire right away
        $cache = new Cache(new FileSystem(), __DIR__ . "/tmp", -1);
        $cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertFalse($cache->has("foo", ["bar" => "baz"], ["blah" => "asdf"]));
        $this->assertNull($cache->get("foo", ["bar" => "baz"], ["blah" => "asdf"]));
    }

    /**
     * Tests checking for a non-existent template
     */
    public function testCheckingForNonExistentTemplate()
    {
        $this->assertFalse($this->cache->has("foo"));
        $this->assertNull($this->cache->get("foo"));
    }

    /**
     * Tests flushing cache
     */
    public function testFlushingCache()
    {
        $this->cache->set("compiled1", "foo", ["bar1" => "baz"], ["blah1" => "asdf"]);
        $this->cache->set("compiled2", "foo", ["bar2" => "baz"], ["blah2" => "asdf"]);
        $this->cache->flush();
        $this->assertFalse($this->cache->has("foo", ["bar1" => "baz"], ["blah1" => "asdf"]));
        $this->assertFalse($this->cache->has("foo", ["bar2" => "baz"], ["blah2" => "asdf"]));
    }

    /**
     * Tests running garbage collection
     */
    public function testGarbageCollection()
    {
        $this->fileSystem->write(__DIR__ . "/tmp/foo", "compiled");
        $this->cache = new Cache($this->fileSystem, __DIR__ . "/tmp", -1);
        $this->cache->gc();
        $this->assertEquals([], $this->fileSystem->getFiles(__DIR__ . "/tmp"));
    }

    /**
     * Tests not creating a directory before attempting to cache views in it
     */
    public function testNotCreatingDirectoryBeforeCaching()
    {
        $this->cache = new Cache($this->fileSystem, __DIR__ . "/verytemporarytmp", 3600);
        $this->cache->set("compiled", "foo", ["bar" => "baz"], ["blah" => "asdf"]);
        $this->assertTrue($this->cache->has("foo", ["bar" => "baz"], ["blah" => "asdf"]));
    }

    /**
     * Tests setting a path and checking for a template
     */
    public function testSettingPathCheckingForExistingTemplate()
    {
        // I know this is also done in setUp(), but we're specifically testing that it works after setting the path
        $this->cache->setPath(__DIR__ . "/tmp");
        $this->testCheckingForExistingTemplate();
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the Composer wrapper
 */
namespace RDev\Framework\Composer;
use RDev\Applications;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array A fully-loaded Composer config */
    private static $fullyLoadedConfig = [
        "name" => "__name__",
        "description" => "__description__",
        "keywords" => ["__keyword1__", "__keyword2__"],
        "authors" => [
            ["name" => "Dave", "email" => "foo@bar.com"]
        ],
        "license" => "__license__",
        "autoload" => [
            "psr-4" => [
                "__namespace__" => [
                    "__namespacepath1__",
                    "__namespacepath2__"
                ],
                "RDev\\" => [
                    "app/rdev",
                    "tests/app/rdev"
                ]
            ]
        ]
    ];
    /** @var Applications\Paths The application paths */
    private $paths = null;
    /** @var Composer The Composer with a fully-loaded config */
    private $composer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->paths = new Applications\Paths([
            "root" => __DIR__ . "/../../../../..",
            "app" => __DIR__ . "/../../../../../app"
        ]);
        $this->composer = new Composer(self::$fullyLoadedConfig, $this->paths);
    }

    /**
     * Tests creating a config from the raw config
     */
    public function testCreatingConfigFromRawConfig()
    {
        $composer = Composer::createFromRawConfig($this->paths);
        $composerFileContents = json_decode(file_get_contents($this->paths["root"] . "/composer.json"), true);
        $this->assertEquals($composerFileContents, $composer->getRawConfig());
    }

    /**
     * Tests getting a single property
     */
    public function testGettingSingleProperty()
    {
        $this->assertEquals("__name__", $this->composer->get("name"));
    }

    /**
     * Tests getting a non-existent single property
     */
    public function testGettingNonExistentSingleProperty()
    {
        $this->assertNull($this->composer->get("doesNotExist"));
    }

    /**
     * Tests getting a single property
     */
    public function testGettingMultiDimensionalProperty()
    {
        $this->assertEquals(
            ["__namespacepath1__", "__namespacepath2__"],
            $this->composer->get("autoload.psr-4.__namespace__")
        );
    }

    /**
     * Tests getting a non-existent multi-dimensional property
     */
    public function testGettingNonExistentMultiDimensionalProperty()
    {
        $this->assertNull($this->composer->get("autoload.psr-4.doesNotExist"));
    }

    /**
     * Tests getting the fully qualified class name
     */
    public function testGettingFullyQualifiedClassName()
    {
        $this->assertEquals("RDev\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "RDev"));
        $this->assertEquals("RDev\\Foo\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "RDev\\Foo"));
        $this->assertEquals("RDev\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "RDev\\"));
    }

    /**
     * Tests getting the fully qualified class name of a fully-qualified class
     */
    public function testGettingFullyQualifiedClassNameOfAFullyQualifiedClass()
    {
        $this->assertEquals("RDev\\Foo\\Bar", $this->composer->getFullyQualifiedClassName("RDev\\Foo\\Bar", "RDev"));
    }

    /**
     * Tests getting a non-existent root namespace
     */
    public function testGettingNonExistentRootNamespace()
    {
        $composer = new Composer(["foo" => "bar"], $this->paths);
        $this->assertNull($composer->getRootNamespace());
    }

    /**
     * Tests getting a non-existent root namespace paths
     */
    public function testGettingNonExistentRootNamespacePaths()
    {
        $composer = new Composer(["foo" => "bar"], $this->paths);
        $this->assertNull($composer->getRootNamespacePaths());
    }

    /**
     * Tests getting a path from a class
     */
    public function testGettingPathFromClass()
    {
        $class = "RDev\\Foo\\Bar";
        $this->assertEquals($this->paths["app"] . "/rdev/foo/Bar.php", $this->composer->getClassPath($class));
    }

    /**
     * Tests getting a path from a class in the root namespace
     */
    public function testGettingPathFromClassInRootNamespace()
    {
        $class = "RDev\\Bar";
        $this->assertEquals($this->paths["app"] . "/rdev/Bar.php", $this->composer->getClassPath($class));
    }

    /**
     * Tests getting the raw config
     */
    public function testGettingRawConfig()
    {
        $this->assertEquals(self::$fullyLoadedConfig, $this->composer->getRawConfig());
        // As a sanity check, make sure it's not an empty array
        $this->assertNotEquals([], $this->composer->getRawConfig());
    }

    /**
     * Tests getting the raw config from non-existent Composer config
     */
    public function testGettingRawConfigFromNonExistentComposerConfig()
    {
        $this->paths["root"] = __DIR__;
        $composer = Composer::createFromRawConfig($this->paths);
        $this->assertEquals([], $composer->getRawConfig());
    }

    /**
     * Tests getting the root namespace
     */
    public function testGettingRootNamespace()
    {
        $this->assertEquals("RDev", $this->composer->getRootNamespace());
    }

    /**
     * Tests getting the root namespace paths
     */
    public function testGettingRootNamespacePaths()
    {
        $this->assertEquals(["app/rdev", "tests/app/rdev"], $this->composer->getRootNamespacePaths());
    }

    /**
     * Tests getting the root namespace paths with string path
     */
    public function testGettingRootNamespacePathsWithStringNamespace()
    {
        $composer = new Composer(["autoload" => ["psr-4" => ["RDev\\" => "app/rdev"]]], $this->paths);
        $this->assertEquals(["app/rdev"], $composer->getRootNamespacePaths());
    }

    /**
     * Tests getting the root namespace with string path
     */
    public function testGettingRootNamespaceWithStringNamespace()
    {
        $composer = new Composer(["autoload" => ["psr-4" => ["RDev\\" => "app/rdev"]]], $this->paths);
        $this->assertEquals("RDev", $composer->getRootNamespace());
    }
}
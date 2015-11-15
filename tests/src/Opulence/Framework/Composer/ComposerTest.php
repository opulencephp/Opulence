<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Composer;

use Opulence\Bootstrappers\Paths;

/**
 * Tests the Composer wrapper
 */
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
                "Opulence\\" => [
                    "src/Opulence",
                    "tests/src/Opulence"
                ]
            ]
        ]
    ];
    /** @var Paths The application paths */
    private $paths = null;
    /** @var Composer The Composer with a fully-loaded config */
    private $composer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->paths = new Paths([
            "root" => __DIR__ . "/../../../../..",
            "src" => __DIR__ . "/../../../../../src"
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
     * Tests getting the fully qualified class name
     */
    public function testGettingFullyQualifiedClassName()
    {
        $this->assertEquals("Opulence\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "Opulence"));
        $this->assertEquals("Opulence\\Foo\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "Opulence\\Foo"));
        $this->assertEquals("Opulence\\Bar", $this->composer->getFullyQualifiedClassName("Bar", "Opulence\\"));
    }

    /**
     * Tests getting the fully qualified class name of a fully-qualified class
     */
    public function testGettingFullyQualifiedClassNameOfAFullyQualifiedClass()
    {
        $this->assertEquals("Opulence\\Foo\\Bar",
            $this->composer->getFullyQualifiedClassName("Opulence\\Foo\\Bar", "Opulence"));
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
     * Tests getting a non-existent single property
     */
    public function testGettingNonExistentSingleProperty()
    {
        $this->assertNull($this->composer->get("doesNotExist"));
    }

    /**
     * Tests getting a path from a class
     */
    public function testGettingPathFromClass()
    {
        $class = "Opulence\\Foo\\Bar";
        $this->assertEquals($this->paths["src"] . "/Opulence/Foo/Bar.php", $this->composer->getClassPath($class));
    }

    /**
     * Tests getting a path from a class in the root namespace
     */
    public function testGettingPathFromClassInRootNamespace()
    {
        $class = "Opulence\\Bar";
        $this->assertEquals($this->paths["src"] . "/Opulence/Bar.php", $this->composer->getClassPath($class));
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
        $this->assertEquals("Opulence", $this->composer->getRootNamespace());
    }

    /**
     * Tests getting the root namespace paths
     */
    public function testGettingRootNamespacePaths()
    {
        $this->assertEquals(["src/Opulence", "tests/src/Opulence"], $this->composer->getRootNamespacePaths());
    }

    /**
     * Tests getting the root namespace paths with string path
     */
    public function testGettingRootNamespacePathsWithStringNamespace()
    {
        $composer = new Composer(["autoload" => ["psr-4" => ["Opulence\\" => "src/Opulence"]]], $this->paths);
        $this->assertEquals(["src/Opulence"], $composer->getRootNamespacePaths());
    }

    /**
     * Tests getting the root namespace with string path
     */
    public function testGettingRootNamespaceWithStringNamespace()
    {
        $composer = new Composer(["autoload" => ["psr-4" => ["Opulence\\" => "src/Opulence"]]], $this->paths);
        $this->assertEquals("Opulence", $composer->getRootNamespace());
    }

    /**
     * Tests getting a single property
     */
    public function testGettingSingleProperty()
    {
        $this->assertEquals("__name__", $this->composer->get("name"));
    }
}
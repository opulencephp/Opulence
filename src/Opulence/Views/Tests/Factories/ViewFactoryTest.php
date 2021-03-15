<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Factories;

use InvalidArgumentException;
use Opulence\Views\Factories\IO\IViewNameResolver;
use Opulence\Views\Factories\IO\IViewReader;
use Opulence\Views\Factories\ViewFactory;
use Opulence\Views\IView;
use Opulence\Views\Tests\Factories\Mocks\BarBuilder;
use Opulence\Views\Tests\Factories\Mocks\FooBuilder;

/**
 * Tests the view factory
 */
class ViewFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var IViewNameResolver|\PHPUnit_Framework_MockObject_MockObject The view name resolver to use in tests */
    private $viewNameResolver = null;
    /** @var IViewReader|\PHPUnit_Framework_MockObject_MockObject The view reader to use in tests */
    private $viewReader = null;
    /** @var ViewFactory The view factory to use in tests */
    private $viewFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->viewNameResolver = $this->createMock(IViewNameResolver::class);
        $this->viewReader = $this->createMock(IViewReader::class);
        $this->viewReader->expects($this->any())
            ->method('read')
            ->willReturn('foo');
        $this->viewFactory = $this->getMockBuilder(ViewFactory::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->viewNameResolver, $this->viewReader])
            ->getMock();
    }

    /**
     * Tests checking if views exist
     */
    public function testCheckingIfViewExists()
    {
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn('foo');
        $this->viewNameResolver->expects($this->at(1))
            ->method('resolve')
            ->willThrowException(new InvalidArgumentException());
        $this->assertTrue($this->viewFactory->hasView('foo'));
        $this->assertFalse($this->viewFactory->hasView('bar'));
    }

    /**
     * Tests registering a builder
     */
    public function testRegisteringBuilder()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering builders to multiple paths
     */
    public function testRegisteringBuilderToMultiplePaths()
    {
        $this->viewFactory->registerBuilder(['TestWithDefaultTagDelimiters', 'TestWithCustomTagDelimiters'],
            function (IView $view) {
                return (new FooBuilder())->build($view);
            });
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $this->viewNameResolver->expects($this->at(1))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithCustomTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
        $view = $this->viewFactory->createView('TestWithCustomTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering a builder for a view name and then creating that view with the exact same view name
     */
    public function testRegisteringBuilderWithExactSameNameAsView()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters.html', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters.html');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering a builder for a view with an extension and then creating that view without an extension
     */
    public function testRegisteringBuilderWithExtensionAndCreatingSameViewWithoutExtension()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters.html', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering a builder with the same basename as a view, but resolves to a different view file
     */
    public function testRegisteringBuilderWithSameBasenameAsViewButResolvesToDifferentViewFile()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters.foo.php', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithCustomTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters.foo.html');
        $this->assertEquals([], $view->getVars());
    }

    /**
     * Tests registering a builder with the same filename as a view, but resolves to a different view file
     */
    public function testRegisteringBuilderWithSameFilenameAsViewButResolvesToDifferentViewFile()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters.foo', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $this->viewNameResolver->expects($this->at(0))
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithCustomTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters.bar');
        $this->assertEquals([], $view->getVars());
    }

    /**
     * Tests registering a builder for a view without an extension and then creating that view with an extension
     */
    public function testRegisteringBuilderWithoutExtensionAndCreatingSameViewWithExtension()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters.html');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering a closure builder
     */
    public function testRegisteringClosureBuilder()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters', function (IView $view) {
            $view->setVar('foo', 'bar');

            return $view;
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
    }

    /**
     * Tests registering multiple builders
     */
    public function testRegisteringMultipleBuilders()
    {
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters', function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewFactory->registerBuilder('TestWithDefaultTagDelimiters', function (IView $view) {
            return (new BarBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method('resolve')
            ->willReturn(__DIR__ . '/../files/TestWithDefaultTagDelimiters.html');
        $view = $this->viewFactory->createView('TestWithDefaultTagDelimiters');
        $this->assertEquals('bar', $view->getVar('foo'));
        $this->assertEquals('baz', $view->getVar('bar'));
    }
}

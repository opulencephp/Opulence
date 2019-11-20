<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests;

use InvalidArgumentException;
use Opulence\Views\IO\IViewNameResolver;
use Opulence\Views\IO\IViewReader;
use Opulence\Views\ViewFactory;
use Opulence\Views\IView;
use Opulence\Views\Tests\Mocks\BarBuilder;
use Opulence\Views\Tests\Mocks\FooBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the view factory
 */
class ViewFactoryTest extends TestCase
{
    /** @var IViewNameResolver|MockObject The view name resolver to use in tests */
    private IViewNameResolver $viewNameResolver;
    /** @var IViewReader|MockObject The view reader to use in tests */
    private IViewReader $viewReader;
    private ViewFactory $viewFactory;

    protected function setUp(): void
    {
        $this->viewNameResolver = $this->createMock(IViewNameResolver::class);
        $this->viewReader = $this->createMock(IViewReader::class);
        $this->viewReader->expects($this->any())
            ->method('read')
            ->willReturn('foo');
        $this->viewFactory = new ViewFactory($this->viewNameResolver, $this->viewReader);
    }

    public function testCheckingIfViewExists(): void
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

    public function testRegisteringBuilder(): void
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

    public function testRegisteringBuilderToMultiplePaths(): void
    {
        $this->viewFactory->registerBuilder(
            ['TestWithDefaultTagDelimiters', 'TestWithCustomTagDelimiters'],
            function (IView $view) {
                return (new FooBuilder())->build($view);
            }
        );
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

    public function testRegisteringBuilderWithExactSameNameAsView(): void
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

    public function testRegisteringBuilderWithExtensionAndCreatingSameViewWithoutExtension(): void
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
    public function testRegisteringBuilderWithSameBasenameAsViewButResolvesToDifferentViewFile(): void
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
    public function testRegisteringBuilderWithSameFilenameAsViewButResolvesToDifferentViewFile(): void
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

    public function testRegisteringBuilderWithoutExtensionAndCreatingSameViewWithExtension(): void
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

    public function testRegisteringClosureBuilder(): void
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

    public function testRegisteringMultipleBuilders(): void
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

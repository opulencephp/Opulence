<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http\Testing\PhpUnit\Mocks;

use Opulence\Debug\Exceptions\Handlers\ExceptionHandler;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Opulence\Environments\Environment;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Framework\Http\Bootstrappers\RequestBootstrapper;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ResponseAssertions;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ViewAssertions;
use Opulence\Framework\Http\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Opulence\Framework\Routing\Bootstrappers\RouterBootstrapper;
use Opulence\Http\Responses\Response;
use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\BootstrapperResolver;
use Opulence\Ioc\Bootstrappers\Dispatchers\BootstrapperDispatcher;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Router;

/**
 * Mocks the HTTP integration test for use in testing
 */
class IntegrationTestCase extends BaseIntegrationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        RequestBootstrapper::class,
        RouterBootstrapper::class
    ];

    /**
     * Gets the response assertions
     *
     * @return ResponseAssertions
     */
    public function getResponseAssertions() : ResponseAssertions
    {
        return $this->assertResponse;
    }

    /**
     * @return Router
     */
    public function getRouter() : Router
    {
        return $this->router;
    }

    /**
     * Gets the view assertions
     *
     * @return ViewAssertions The view assertions
     */
    public function getViewAssertions() : ViewAssertions
    {
        return $this->assertView;
    }

    /**
     * Sets up the application and container
     */
    public function setUp()
    {
        Config::setCategory('paths', [
            'configs' => realpath(__DIR__ . '/../../configs'),
            'root' => realpath(__DIR__ . '/../../../../../..'),
            'src' => realpath(__DIR__ . '/../../../../../../src')
        ]);
        // Create and bind all of the components of our application
        // Purposely set this to a weird value so we can test that it gets overwritten with the "test" environment
        $this->environment = new Environment();
        $this->container = new Container();
        $this->container->bindInstance(Environment::class, $this->environment);
        $this->container->bindInstance(IContainer::class, $this->container);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry();
        $bootstrapperDispatcher = new BootstrapperDispatcher(
            $this->container,
            $bootstrapperRegistry,
            new BootstrapperResolver()
        );
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $bootstrapperDispatcher->dispatch(false);

        parent::setUp();
    }

    /**
     * @inheritdoc
     */
    protected function getExceptionHandler() : IExceptionHandler
    {
        return $this->getMockBuilder(ExceptionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function getExceptionRenderer() : IExceptionRenderer
    {
        /** @var IExceptionRenderer|\PHPUnit_Framework_MockObject_MockObject $renderer */
        $renderer = $this->createMock(IExceptionRenderer::class);
        /** @var Response|\PHPUnit_Framework_MockObject_MockObject $response */
        $response = $this->createMock(Response::class);
        // Mock a 404 status code because this will primarily be used for missing routes in our tests
        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(404);
        $renderer->expects($this->any())
            ->method('getResponse')
            ->willReturn($response);

        return $renderer;
    }

    /**
     * @inheritdoc
     */
    protected function getGlobalMiddleware() : array
    {
        return [];
    }
}

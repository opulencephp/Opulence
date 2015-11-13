<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines the base application test case
 */
namespace Opulence\Framework\Testing\PhpUnit;

use Monolog\Logger;
use Opulence\Applications\Application;
use Opulence\Ioc\IContainer;
use PHPUnit_Framework_TestCase;

abstract class ApplicationTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Application The application */
    protected $application = null;
    /** @var IContainer The IoC container */
    protected $container = null;

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        $this->application->shutdown();
    }

    /**
     * Gets the kernel logger
     *
     * @return Logger The logger to use in the kernel
     */
    abstract protected function getKernelLogger();

    /**
     * Sets the instance of the application and IoC container to use in tests
     */
    abstract protected function setApplicationAndIocContainer();
}
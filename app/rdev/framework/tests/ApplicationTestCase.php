<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base application test case
 */
namespace RDev\Framework\Tests;
use PHPUnit_Framework_TestCase;
use Monolog\Logger;
use RDev\Applications\Application;

abstract class ApplicationTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Application The application */
    protected $application = null;

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

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
     * Sets the instance of the application to use in tests
     */
    abstract protected function setApplication();
}
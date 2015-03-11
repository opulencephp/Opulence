<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base application test case
 */
namespace RDev\Framework\Tests;
use RDev\Applications;

abstract class ApplicationTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Applications\Application The application */
    protected $application = null;

    /**
     * @return Applications\Application
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
     * Sets the instance of the application to use in tests
     */
    abstract protected function setApplication();
}
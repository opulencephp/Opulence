<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the application for use in testing
 */
namespace RDev\Tests\Applications\Mocks;
use RDev\Applications;

class Application extends Applications\Application
{
    /**
     * {@inheritdoc}
     * We don't want to actually write anything to the client while testing
     */
    protected function doShutdown()
    {
        // Don't do anything
    }
} 
<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a Monolog handler for use in testing
 * We don't want to actually write errors to the logs during the tests, so we must create a dummy handler to do that
 */
namespace RDev\Tests\Models\Applications\Mocks;
use Monolog\Handler;

class MonologHandler extends Handler\AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        // Don't do anything
    }
} 
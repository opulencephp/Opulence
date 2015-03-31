<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a Monolog handler for use in testing
 * We don't want to actually write errors to the logs during the tests, so we must create a dummy handler to do that
 */
namespace RDev\Tests\Applications\Mocks;
use Monolog\Handler\AbstractHandler;

class MonologHandler extends AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        // Don't do anything
    }
} 
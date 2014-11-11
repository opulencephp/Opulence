<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Mocks the environment detector for use in testing
 */
namespace RDev\Tests\Applications\Mocks;
use RDev\Applications;

class EnvironmentDetector implements Applications\IEnvironmentDetector
{
    /**
     * {@inheritdoc}
     */
    public function detect()
    {
        return Applications\Environment::PRODUCTION;
    }
}
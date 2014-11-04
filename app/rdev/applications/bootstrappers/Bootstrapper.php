<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a bootstrapper
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications;

abstract class Bootstrapper implements IBootstrapper
{
    /** @var Applications\Application The application we're bootstrapping */
    protected $application = null;

    /**
     * {@inheritdoc}
     */
    public function setApplication(Applications\Application $application)
    {
        $this->application = $application;
    }
}
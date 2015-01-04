<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a bootstrapper for use in tests
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;
use RDev\Applications\Bootstrappers;
use RDev\Tests\Mocks;

class Bootstrapper implements Bootstrappers\IBootstrapper
{
    /** @var Mocks\User The user to use in tests */
    private $user = null;

    /**
     * Gets the user after this has been run
     *
     * @return Mocks\User The user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->user = new Mocks\User(1, "bootstrap user");
    }
}
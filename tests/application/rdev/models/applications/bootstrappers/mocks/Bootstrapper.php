<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a bootstrapper for use in tests
 */
namespace RDev\Tests\Models\Applications\Bootstrappers\Mocks;
use RDev\Models\Applications\Bootstrappers;
use RDev\Tests\Models\Mocks;

class Bootstrapper extends Bootstrappers\Bootstrapper
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
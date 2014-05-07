<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login page
 */
namespace RamODev\Application\TBA\Web\Websites\Pages;
use RamODev\Application\Shared\Web\Websites\Pages;

class Login extends Pages\Generic
{
    public function __construct()
    {
        parent::__construct();

        $formPagelet = new Pages\Template(__DIR__ . "/pagelets/templates/LoginForm.html");
        $this->setTitle("Log In");
        $this->setMetaDescription("Log in to your account");
        $this->addMetaKeywords(array("Log in", "my account"));
        $this->setTags(array(
            "bodyContent" => $formPagelet->getOutput()
        ));
    }
} 
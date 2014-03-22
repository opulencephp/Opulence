<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login page
 */
namespace RamODev\Websites\Pages;

class Login extends Generic
{
    public function __construct()
    {
        parent::__construct();

        $formPagelet = new Template(__DIR__ . "/pagelets/templates/LoginForm.html");
        $this->setTitle("Log In");
        $this->setMetaDescription("Log in to your account");
        $this->addMetaKeywords(array("Log in", "my account"));
        $this->setTags(array(
            "bodyContent" => $formPagelet->getOutput()
        ));
    }
} 
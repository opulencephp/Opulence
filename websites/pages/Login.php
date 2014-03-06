<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the login page
 */
namespace RamODev\Websites\Pages;

class Login extends Template
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/templates/Generic.html");

        $formPagelet = new Template(__DIR__ . "/pagelets/templates/LoginForm.html");
        $this->setTags(array(
            "pageTitle" => "Log In",
            "bodyContent" => $formPagelet->getHTML()
        ));
    }
} 
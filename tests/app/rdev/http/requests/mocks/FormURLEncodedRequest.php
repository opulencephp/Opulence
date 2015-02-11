<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a form url-encoded request class for use in testing
 */
namespace RDev\Tests\HTTP\Requests\Mocks;
use RDev\HTTP\Requests;

class FormURLEncodedRequest extends Requests\Request
{
    /**
     * {@inheritdoc}
     */
    public function getRawBody()
    {
        return http_build_query(["foo" => "bar"]);
    }
}
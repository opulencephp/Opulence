<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a JSON request class for use in testing
 */
namespace RDev\Tests\HTTP\Requests\Mocks;
use RDev\HTTP\Requests;

class JSONRequest extends Requests\Request
{
    /**
     * {@inheritdoc}
     */
    public function getRawBody()
    {
        return json_encode(["foo" => "bar"]);
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a JSON request class for use in testing
 */
namespace Opulence\Tests\HTTP\Requests\Mocks;
use Opulence\HTTP\Requests\Request;

class JSONRequest extends Request
{
    /**
     * {@inheritdoc}
     */
    public function getRawBody()
    {
        return json_encode(["foo" => "bar"]);
    }

    /**
     * {@inheritdoc}
     */
    public function isJSON()
    {
        return true;
    }
}
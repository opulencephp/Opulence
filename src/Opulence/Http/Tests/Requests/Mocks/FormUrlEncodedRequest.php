<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Http\Tests\Requests\Mocks;

use Opulence\Http\Requests\Request;

/**
 * Mocks a form url-encoded request class for use in testing
 */
class FormUrlEncodedRequest extends Request
{
    /**
     * @inheritdoc
     */
    public function getRawBody() : string
    {
        return http_build_query(['foo' => 'bar']);
    }
}

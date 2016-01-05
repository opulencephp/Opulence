<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Http\Requests\Mocks;

use Opulence\Http\Requests\Request;

/**
 * Mocks a form url-encoded request class for use in testing
 */
class FormUrlEncodedRequest extends Request
{
    /**
     * @inheritdoc
     */
    public function getRawBody()
    {
        return http_build_query(["foo" => "bar"]);
    }
}
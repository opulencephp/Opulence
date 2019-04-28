<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http\Tests\Requests\Mocks;

use Opulence\Http\Requests\Request;

/**
 * Mocks a JSON request class for use in testing
 */
class JsonRequest extends Request
{
    /**
     * @inheritdoc
     */
    public function getRawBody(): string
    {
        return json_encode(['foo' => 'bar']);
    }

    /**
     * @inheritdoc
     */
    public function isJson(): bool
    {
        return true;
    }
}

<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\Adapters\Pdo\Mocks;

use Opulence\Databases\Adapters\Pdo\Statement as BaseStatement;
use PDO;

/**
 * Mocks the PDO statement for use in testing
 */
class Statement extends BaseStatement
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     * We have to mock this because attempting to bind a value to an unopened connection will always fail
     */
    public function bindValues(array $values)
    {
        foreach ($values as $parameterName => $value) {
            if (!is_array($value)) {
                $value = [$value, PDO::PARAM_STR];
            }

            // Here we don't actually attempt to bind the value
            if (count($value) !== 2) {
                return false;
            }
        }

        return true;
    }
}

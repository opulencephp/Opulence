<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Adapters\Pdo;

use Opulence\Databases\Tests\Adapters\Pdo\Mocks\Statement;
use PDO;

/**
 * Tests the PDO statement
 */
class StatementTest extends \PHPUnit\Framework\TestCase
{
    public function testBindingInvalidAssociativeArray(): void
    {
        $statement = new Statement();
        $values = ['foo' => 'bar', 'id' => [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    public function testBindingInvalidIndexedArray(): void
    {
        $statement = new Statement();
        $values = ['bar', [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    public function testBindingValidAssociativeArray(): void
    {
        $statement = new Statement();
        $values = ['foo' => 'bar', 'id' => [1, PDO::PARAM_INT]];
        $this->assertTrue($statement->bindValues($values));
    }

    public function testBindingValidIndexedArray(): void
    {
        $statement = new Statement();
        $values = ['bar', 1, PDO::PARAM_INT];
        $this->assertTrue($statement->bindValues($values));
    }
}

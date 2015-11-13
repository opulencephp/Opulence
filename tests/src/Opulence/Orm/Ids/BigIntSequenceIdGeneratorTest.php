<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\Ids;

use Opulence\Tests\Mocks\User;
use Opulence\Tests\Databases\Mocks\Connection;
use Opulence\Tests\Databases\Mocks\Server;

/**
 * Tests the big integer sequence Id generator
 */
class BigIntSequenceIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests generating an Id
     */
    public function testGeneratingId()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entity = new User(-1, "foo");
        $idGenerator = new BigIntSequenceIdGenerator("foo");
        $this->assertSame("1", $idGenerator->generate($entity, $connection));
        $this->assertSame("2", $idGenerator->generate($entity, $connection));
    }
} 
<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications\Environments\Hosts;

/**
 * Tests the host regex
 */
class HostRegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that delimiters are added
     */
    public function testDelimitersAreAdded()
    {
        $host = new HostRegex(".*");
        $this->assertEquals("#.*#", $host->getValue());
    }
}
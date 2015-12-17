<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules\Factories;

use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Tests the rules factory
 */
class RulesFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that rules are created
     */
    public function testRulesCreated()
    {
        /** @var RuleExtensionRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->getMock(RuleExtensionRegistry::class);
        $factory = new RulesFactory($registry);
        $this->assertInstanceOf(Rules::class, $factory->createRules());
    }
}
<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Errors;

use InvalidArgumentException;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;

/**
 * Tests the error template registry
 */
class ErrorTemplateRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ErrorTemplateRegistry The error template registry to use in tests */
    private $registry;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->registry = new ErrorTemplateRegistry();
    }

    /**
     * Tests that an empty string is returned when no template exists
     */
    public function testEmptyStringReturnedWhenNoTemplateExists(): void
    {
        $this->assertEquals('', $this->registry->getErrorTemplate('foo', 'bar'));
    }

    /**
     * Tests that an exception is thrown with an empty key in a config
     */
    public function testExceptionThrownWithEmptyKeyInConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerErrorTemplatesFromConfig(['' => 'template']);
    }

    /**
     * Tests that an exception is thrown with an invalid field template key
     */
    public function testExceptionThrownWithInvalidFieldTemplateKeyInConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerErrorTemplatesFromConfig([' . ' => 'template']);
    }

    /**
     * Tests that a field template overrides a global template
     */
    public function testFieldTemplateOverridesGlobalTemplate(): void
    {
        $this->registry->registerFieldErrorTemplate('field', 'foo', 'field template');
        $this->registry->registerGlobalErrorTemplate('foo', 'global template');
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests that a field template overrides a global template when registering from a config
     */
    public function testFieldTemplateOverridesGlobalTemplateWhenRegisteringFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'field.foo' => 'field template',
            'foo' => 'global template'
        ]);
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests overwriting a global template
     */
    public function testOverwritingGlobalTemplate(): void
    {
        $this->registry->registerGlobalErrorTemplate('foo', 'template 1');
        $this->registry->registerGlobalErrorTemplate('foo', 'template 2');
        $this->assertEquals('template 2', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests registering a field template
     */
    public function testRegisteringFieldTemplate(): void
    {
        $this->registry->registerFieldErrorTemplate('field', 'foo', 'bar baz');
        $this->assertEquals('bar baz', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests registering a field template from a config
     */
    public function testRegisteringFieldTemplateFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'field.foo' => 'field template'
        ]);
        $this->assertEquals('field template', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests registering a global template
     */
    public function testRegisteringGlobalTemplate(): void
    {
        $this->registry->registerGlobalErrorTemplate('foo', 'bar baz');
        $this->assertEquals('bar baz', $this->registry->getErrorTemplate('field', 'foo'));
    }

    /**
     * Tests registering a global template from a config
     */
    public function testRegisteringGlobalTemplateFromConfig(): void
    {
        $this->registry->registerErrorTemplatesFromConfig([
            'foo' => 'global template'
        ]);
        $this->assertEquals('global template', $this->registry->getErrorTemplate('field', 'foo'));
    }
}

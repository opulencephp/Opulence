<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit;

use Opulence\Applications\Application;
use Opulence\Environments\Environment;
use Opulence\Ioc\IContainer;
use PHPUnit_Framework_TestCase;

/**
 * Defines the base application test case
 */
abstract class ApplicationTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Application The application */
    protected $application = null;
    /** @var Environment The current environment */
    protected $environment = null;
    /** @var IContainer The IoC container */
    protected $container = null;

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        $this->application->shutDown();
    }
}
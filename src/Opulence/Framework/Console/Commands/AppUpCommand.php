<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Bootstrappers\Paths;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the application-up command
 */
class AppUpCommand extends Command
{
    /** @var Paths The application paths */
    private $paths = null;

    /**
     * @param Paths $paths The application paths
     */
    public function __construct(Paths $paths)
    {
        parent::__construct();

        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("app:up")
            ->setDescription("Takes the application out of maintenance mode");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        @unlink("{$this->paths["tmp.framework.http"]}/down");
        $response->writeln("<success>Application out of maintenance mode</success>");
    }
}
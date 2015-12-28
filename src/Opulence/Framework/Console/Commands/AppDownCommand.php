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
 * Defines the application-down command
 */
class AppDownCommand extends Command
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
        $this->setName("app:down")
            ->setDescription("Puts the application into maintenance mode");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        if (file_put_contents("{$this->paths["tmp.framework.http"]}/down", "down") === false) {
            $response->writeln("<error>Failed to put application into maintenance mode</error>");
        } else {
            $response->writeln("<success>Application in maintenance mode</success>");
        }
    }
}
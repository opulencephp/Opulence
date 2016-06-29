<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Bootstrappers\Paths;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the encryption password generator command
 */
class EncryptionPasswordGenerationCommand extends Command
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
        $this->setName("encryption:generatepassword")
            ->setDescription("Creates an encryption password")
            ->addOption(new Option(
                "show",
                "s",
                OptionTypes::NO_VALUE,
                "Whether to just show the new password or replace it in the environment config"
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $password = bin2hex(random_bytes(16));
        $environmentConfigPath = $this->paths["config"] . "/environment/.env.app.php";

        if (!$this->optionIsSet("show") && file_exists($environmentConfigPath)) {
            $contents = file_get_contents($environmentConfigPath);
            $newContents = preg_replace("/\"ENCRYPTION_(PASSWORD|KEY)\",\s*\"[^\"]*\"/U", '"ENCRYPTION_PASSWORD", "' . $password . '"',
                $contents);
            file_put_contents($environmentConfigPath, $newContents);
        }

        $response->writeln("Generated key: <info>$password</info>");
    }
}
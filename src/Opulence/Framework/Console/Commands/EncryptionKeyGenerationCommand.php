<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Applications\Paths;
use Opulence\Cryptography\Utilities\Strings;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the encryption key generator command
 */
class EncryptionKeyGenerationCommand extends Command
{
    /** @var Strings The string utility */
    private $strings = null;
    /** @var Paths The application paths */
    private $paths = null;

    /**
     * @param Strings $strings The string utility
     * @param Paths $paths The application paths
     */
    public function __construct(Strings $strings, Paths $paths)
    {
        parent::__construct();

        $this->strings = $strings;
        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("encryption:generatekey")
            ->setDescription("Creates an encryption key")
            ->addOption(new Option(
                "show",
                "s",
                OptionTypes::NO_VALUE,
                "Whether to just show the new key or replace it in the environment config"
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $key = $this->strings->generateRandomString(32);
        $environmentConfigPath = $this->paths["configs"] . "/environment/.env.app.php";

        if (!$this->optionIsSet("show") && file_exists($environmentConfigPath)) {
            $contents = file_get_contents($environmentConfigPath);
            $newContents = preg_replace("/\"ENCRYPTION_KEY\",\s*\"[^\"]*\"/U", '"ENCRYPTION_KEY", "' . $key . '"',
                $contents);
            file_put_contents($environmentConfigPath, $newContents);
        }

        $response->writeln("Generated key: <info>$key</info>");
    }
}
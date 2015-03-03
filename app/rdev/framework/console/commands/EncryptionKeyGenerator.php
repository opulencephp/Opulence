<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the encryption key generator command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications;
use RDev\Cryptography\Utilities;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class EncryptionKeyGenerator extends Commands\Command
{
    /** @var Utilities\Strings The string utility */
    private $stringUtility = null;
    /** @var Applications\Paths The application paths */
    private $paths = null;

    /**
     * @param Utilities\Strings $stringUtility The string utility
     * @param Applications\Paths $paths The application paths
     */
    public function __construct(Utilities\Strings $stringUtility, Applications\Paths $paths)
    {
        parent::__construct();

        $this->stringUtility = $stringUtility;
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("encryption:generatekey")
            ->setDescription("Creates an encryption key")
            ->addOption(new Requests\Option(
                "show",
                "s",
                Requests\OptionTypes::NO_VALUE,
                "Whether to just show the new key or replace it in the environment config"
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $key = $this->stringUtility->generateRandomString(32);
        $environmentConfigPath = $this->paths["configs"] . "/environment/.env.app.php";

        if(!$this->optionIsSet("show") && file_exists($environmentConfigPath))
        {
            $contents = file_get_contents($environmentConfigPath);
            $newContents = preg_replace("/\"ENCRYPTION_KEY\",\s*\"[^\"]*\"/U", '"ENCRYPTION_KEY", "' . $key . '"', $contents);
            file_put_contents($environmentConfigPath, $newContents);
        }

        $response->writeln("Generated key: <info>$key</info>");
    }
}
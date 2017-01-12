<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Cryptography\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the encryption key generator command
 */
class EncryptionKeyGenerationCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('encryption:generatekey')
            ->setDescription('Creates an encryption key')
            ->addOption(new Option(
                'show',
                's',
                OptionTypes::NO_VALUE,
                'Whether to just show the new key or replace it in the environment config'
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        // Create a suitably-long key that can be used with sha512
        $key = \bin2hex(\random_bytes(32));
        $environmentConfigPath = Config::get('paths', 'config') . '/environment/.env.app.php';

        if (!$this->optionIsSet('show') && file_exists($environmentConfigPath)) {
            $contents = file_get_contents($environmentConfigPath);
            $newContents = preg_replace("/\"ENCRYPTION_KEY\",\s*\"[^\"]*\"/U", '"ENCRYPTION_KEY", "' . $key . '"',
                $contents);
            file_put_contents($environmentConfigPath, $newContents);
        }

        $response->writeln("Generated key: <info>$key</info>");
    }
}

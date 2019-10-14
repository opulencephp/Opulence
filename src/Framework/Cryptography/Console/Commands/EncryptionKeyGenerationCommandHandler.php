<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Cryptography\Console\Commands;

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the encryption key generator command handler
 */
final class EncryptionKeyGenerationCommandHandler implements ICommandHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        // Create a suitably-long key that can be used with sha512
        $key = \bin2hex(\random_bytes(32));
        $environmentConfigPath = Config::get('paths', 'config') . '/environment/.env.app.php';

        if (!array_key_exists('show', $input->options) && file_exists($environmentConfigPath)) {
            $contents = file_get_contents($environmentConfigPath);
            $newContents = preg_replace(
                "/\"ENCRYPTION_KEY\",\s*\"[^\"]*\"/U",
                '"ENCRYPTION_KEY", "' . $key . '"',
                $contents
            );
            file_put_contents($environmentConfigPath, $newContents);
        }

        $output->writeln("Generated key: <info>$key</info>");
    }
}

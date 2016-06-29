<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the encryption key generator command
 *
 * @deprecated since v1.0.0-beta4
 */
class EncryptionKeyGenerationCommand extends EncryptionPasswordGenerationCommand
{
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
        $deprecationMessage = '"encryption:generatekey" has been deprecated in favor of "encryption:generatepassword"';
        trigger_error($deprecationMessage, E_USER_DEPRECATED);
        parent::doExecute($response);
    }
}
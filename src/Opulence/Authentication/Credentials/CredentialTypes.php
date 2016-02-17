<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

/**
 * Defines the different credential types
 */
class CredentialTypes
{
    /** A username/password credential */
    const USERNAME_PASSWORD = "USERNAME_PASSWORD";
    /** An API token credential */
    const API_TOKEN = "API_TOKEN";
}
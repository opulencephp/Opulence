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
 * Defines the different types of credentials
 */
class CredentialTypes
{
    /** A password */
    const PASSWORD = 1;
    /** A login credential */
    const LOGIN = 2;
    /** A credential used to authenticate the identity of an entity, but not log it in */
    const AUTHENTICATION = 3;
} 
<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Credentials\Factories;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\ISubject;

/**
 * Defines the interface for credential factories to implement
 */
interface ICredentialFactory
{
    /**
     * Creates a credential for a subject
     *
     * @param ISubject $subject The subject whose credential we're creating
     * @return ICredential The credential
     */
    public function createCredentialForSubject(ISubject $subject): ICredential;
}

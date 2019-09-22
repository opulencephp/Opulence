<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication;

/**
 * Defines the current authentication context
 */
class AuthenticationContext implements IAuthenticationContext
{
    /** @var ISubject|null The current subject */
    private ?ISubject $subject;
    /** @var string The current authentication status */
    private string $status;

    /**
     * @param ISubject|null $subject The current subject
     * @param string $status The current authentication status
     */
    public function __construct(
        ISubject $subject = null,
        string $status = AuthenticationStatusTypes::UNAUTHENTICATED
    ) {
        if ($subject !== null) {
            $this->setSubject($subject);
        }

        $this->setStatus($status);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function getSubject(): ?ISubject
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function isAuthenticated(): bool
    {
        return $this->status === AuthenticationStatusTypes::AUTHENTICATED;
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function setSubject(ISubject $subject): void
    {
        $this->subject = $subject;
    }
}

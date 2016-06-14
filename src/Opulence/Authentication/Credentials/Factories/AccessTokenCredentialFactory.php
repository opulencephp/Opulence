<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Factories;

use DateInterval;
use Opulence\Authentication\Credentials\CredentialTypes;
use Opulence\Authentication\ISubject;
use Opulence\Authentication\Roles\Orm\IRoleRepository;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use Opulence\Authentication\Users\Orm\IUserRepository;

/**
 * Defines the access token credential factory
 */
class AccessTokenCredentialFactory extends JwtCredentialFactory
{
    /** @var IUserRepository The user repository */
    protected $userRepository = null;
    /** @var IRoleRepository The role repository */
    protected $roleRepository = null;

    /**
     * @inheritdoc
     * @param IUserRepository $userRepository The user repository
     * @param IRoleRepository $roleRepository The role repository
     * @param string $clientId The Id of the client sending the access token
     * @param string $resourceServerUri The URI of the resource server
     */
    public function __construct(
        IUserRepository $userRepository,
        IRoleRepository $roleRepository,
        ISigner $signer,
        string $issuer,
        string $clientId,
        string $resourceServerUri,
        DateInterval $validFromInterval,
        DateInterval $validToInterval
    ) {
        parent::__construct($signer, $issuer, [$clientId, $resourceServerUri], $validFromInterval, $validToInterval);

        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @inheritdoc
     */
    protected function addCustomClaims(JwtPayload $payload, ISubject $subject)
    {
        $payload->add("roles", $this->roleRepository->getRoleNamesForSubject($subject->getPrimaryPrincipal()->getId()));
        $userClaims = [
            "username" => ""
        ];
        $user = $this->userRepository->getById($subject->getPrimaryPrincipal()->getId());

        if ($user !== null) {
            $userClaims["username"] = $user->getUsername();
        }

        $payload->add("user", $userClaims);
    }

    /**
     * @inheritdoc
     */
    protected function getCredentialType() : string
    {
        return CredentialTypes::JWT_ACCESS_TOKEN;
    }
}
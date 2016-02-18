<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens;

use DateTimeImmutable;

/**
 * Defines a password token
 */
class Password extends Token
{
    /**
     * @inheritdoc
     */
    public function __construct(
        $id,
        $userId,
        string $hashedValue,
        DateTimeImmutable $validFrom,
        DateTimeImmutable $validTo,
        bool $isActive
    ) {
        parent::__construct($id, $userId, Algorithms::BCRYPT, $hashedValue, $validFrom, $validTo, $isActive);
    }

    /**
     * @inheritdoc
     */
    public static function hash($algorithm, string $unhashedValue, array $options = []) : string
    {
        // Always use Bcrypt
        return parent::hash(PASSWORD_BCRYPT, $unhashedValue, $options);
    }
}
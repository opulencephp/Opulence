<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Sessions\Ids\Generators;

use Opulence\Cryptography\Utilities\Strings;

/**
 * Defines the session Id generator
 */
class IdGenerator implements IIdGenerator
{
    /** The default length of an Id */
    const DEFAULT_LENGTH = 40;
    /** @var Strings The strings utility */
    private $strings = null;

    /**
     * @param Strings $strings The strings utility
     */
    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }

    /**
     * @inheritdoc
     */
    public function generate($length = self::DEFAULT_LENGTH)
    {
        return $this->strings->generateRandomString($length);
    }

    /**
     * @inheritdoc
     */
    public function idIsValid($id) : bool
    {
        $regex = sprintf(
            "/^[a-z0-9]{%d,%d}$/i",
            self::MIN_LENGTH,
            self::MAX_LENGTH
        );

        return is_string($id) && preg_match($regex, $id);
    }
}
<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Ids\Generators;

/**
 * Defines a UUID V4 generator
 */
class UuidV4Generator implements IIdGenerator
{
    /**
     * @inheritdoc
     */
    public function generate($entity)
    {
        $string = \random_bytes(16);
        $string[6] = \chr(\ord($string[6]) & 0x0f | 0x40);
        $string[8] = \chr(\ord($string[8]) & 0x3f | 0x80);

        return \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($string), 4));
    }

    /**
     * @inheritdoc
     */
    public function getEmptyValue($entity)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isPostInsert() : bool
    {
        return false;
    }
}

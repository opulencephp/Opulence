<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc;

use ReflectionNamedType;
use ReflectionParameter;

/**
 * Defines a helper class for reflections
 * Keeps compatibility with PHP7.2 and 8.x
 */
class ReflectionHelper
{
    /**
     * @param ReflectionParameter $parameter
     * @return string|null
     */
    public static function getParameterClassName(ReflectionParameter $parameter) : ?string
    {
        $typeName = static::getParameterTypeName($parameter);
        if (null === $typeName) {
            return null;
        }

        if (class_exists($typeName) || interface_exists($typeName)) {
            return $typeName;
        }

        return null;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return string|null
     */
    public static function getParameterTypeName(ReflectionParameter $parameter) : ?string
    {
        $parameterType = $parameter->getType();
        if ($parameterType === null) {
            return null;
        }

        $className = null;
        if ($parameterType instanceof ReflectionNamedType) {
            return $parameterType->getName();
        }

        if (method_exists($parameterType, 'getTypes')) {
            /** @var ReflectionNamedType $parameterType */
            foreach ($parameterType->getTypes() as $parameterType) {
                return $parameterType->getName();
            }
        }

        return null;
    }
}
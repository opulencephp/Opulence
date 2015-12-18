<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules\Errors\Compilers;

/**
 * Defines the error template compiler
 */
class Compiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile($field, $template, array $args = [])
    {
        $args["field"] = $field;
        $placeholders = array_map(function ($placeholder) {
            return ":$placeholder";
        }, array_keys($args));
        $values = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, array_values($args));
        $compiledTemplate = str_replace($placeholders, $values, $template);
        // Remove leftover placeholders
        $compiledTemplate = preg_replace("/:[a-zA-Z0-9\-_]+\b/", "", $compiledTemplate);

        return trim($compiledTemplate);
    }
}
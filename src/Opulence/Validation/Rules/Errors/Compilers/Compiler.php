<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
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
    public function compile(string $field, string $template, array $args = []) : string
    {
        $args["field"] = $field;
        $placeholders = array_map(function ($placeholder) {
            return ":$placeholder";
        }, array_keys($args));
        $compiledTemplate = str_replace($placeholders, array_values($args), $template);
        // Remove leftover placeholders
        $compiledTemplate = preg_replace("/:[a-zA-Z0-9\-_]+\b/", "", $compiledTemplate);

        return trim($compiledTemplate);
    }
}
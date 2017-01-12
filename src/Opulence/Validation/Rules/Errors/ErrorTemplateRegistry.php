<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Rules\Errors;

use InvalidArgumentException;

/**
 * Defines the error template registry
 */
class ErrorTemplateRegistry
{
    /** @var array The mapping of rule slugs to templates */
    protected $globalTemplates = [];
    /** @var array The mapping of field names to rule slugs and templates */
    protected $fieldTemplates = [];

    /**
     * Gets the error template for a field and rule
     *
     * @param string $field The field whose template we want
     * @param string $ruleSlug The rule slug whose template we want
     * @return string The error template
     */
    public function getErrorTemplate(string $field, string $ruleSlug) : string
    {
        if (isset($this->fieldTemplates[$field][$ruleSlug])) {
            return $this->fieldTemplates[$field][$ruleSlug];
        }

        if (isset($this->globalTemplates[$ruleSlug])) {
            return $this->globalTemplates[$ruleSlug];
        }

        return '';
    }

    /**
     * Registers error templates from a config array
     *
     * @param array $config The mapping of rules to error templates
     *      Global error templates should be formatted "{slug}" => "{template}"
     *      Field error templates should be formatted "{field}.{slug}" => "{template}"
     * @throws InvalidArgumentException Thrown if the config was invalid
     */
    public function registerErrorTemplatesFromConfig(array $config)
    {
        foreach ($config as $key => $template) {
            if (trim($key) === '') {
                throw new InvalidArgumentException('Error template config key cannot be empty');
            }

            if (mb_strpos($key, '.') === false) {
                $this->registerGlobalErrorTemplate($key, $template);
            } else {
                $keyParts = explode('.', $key);

                if (count($keyParts) !== 2 || trim($keyParts[0]) === '' || trim($keyParts[1]) === '') {
                    throw new InvalidArgumentException('Error template config key cannot be empty');
                }

                $this->registerFieldErrorTemplate($keyParts[0], $keyParts[1], $template);
            }
        }
    }

    /**
     * Registers an error template for a specific field and rule
     *
     * @param string $field The field whose template we're registering
     * @param string $ruleSlug The rule slug whose template we're registering
     * @param string $template The template to register
     */
    public function registerFieldErrorTemplate(string $field, string $ruleSlug, string $template)
    {
        if (!isset($this->fieldTemplates[$field])) {
            $this->fieldTemplates[$field] = [];
        }

        $this->fieldTemplates[$field][$ruleSlug] = $template;
    }

    /**
     * Registers a global error template for a rule
     *
     * @param string $ruleSlug The rule slug whose template we're registering
     * @param string $template The template to register
     */
    public function registerGlobalErrorTemplate(string $ruleSlug, string $template)
    {
        $this->globalTemplates[$ruleSlug] = $template;
    }
}

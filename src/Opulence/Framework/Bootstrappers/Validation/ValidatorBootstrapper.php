<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Validation;

use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Validation\Factories\IValidatorFactory;
use Opulence\Validation\Factories\ValidatorFactory;
use Opulence\Validation\Rules\Errors\Compilers\Compiler;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\RuleExtensionRegistry;

/**
 * Defines the validator bootstrapper
 */
abstract class ValidatorBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry = null;
    /** @var ErrorTemplateRegistry The error template registry */
    protected $errorTemplateRegistry = null;
    /** @var ICompiler The error template compiler */
    protected $errorTemplateCompiler = null;
    /** @var RulesFactory The rules factory */
    protected $rulesFactory = null;
    /** @var IValidatorFactory The validator factory */
    protected $validatorFactory = null;

    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [
            ErrorTemplateRegistry::class,
            ICompiler::class,
            RuleExtensionRegistry::class,
            RulesFactory::class,
            IValidatorFactory::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $this->ruleExtensionRegistry = $this->getRuleExtensionRegistry($container);
        $this->registerRuleExtensions($this->ruleExtensionRegistry);
        $this->errorTemplateRegistry = $this->getErrorTemplateRegistry($container);
        $this->registerErrorTemplates($this->errorTemplateRegistry);
        $this->errorTemplateCompiler = $this->getErrorTemplateCompiler($container);
        $this->rulesFactory = $this->getRulesFactory($container);
        $this->validatorFactory = $this->getValidatorFactory($container);
        $container->bindInstance(RuleExtensionRegistry::class, $this->ruleExtensionRegistry);
        $container->bindInstance(ErrorTemplateRegistry::class, $this->errorTemplateRegistry);
        $container->bindInstance(ICompiler::class, $this->errorTemplateCompiler);
        $container->bindInstance(RulesFactory::class, $this->rulesFactory);
        $container->bindInstance(IValidatorFactory::class, $this->validatorFactory);
    }

    /**
     * Registers the error templates
     *
     * @param ErrorTemplateRegistry $errorTemplateRegistry The registry to register to
     */
    abstract protected function registerErrorTemplates(ErrorTemplateRegistry $errorTemplateRegistry);

    /**
     * Gets the error template compiler
     *
     * @param IContainer $container The IoC container
     * @return ICompiler The error template compiler
     */
    protected function getErrorTemplateCompiler(IContainer $container) : ICompiler
    {
        return new Compiler();
    }

    /**
     * Gets the error template registry
     *
     * @param IContainer $container The IoC container
     * @return ErrorTemplateRegistry The error template registry
     */
    protected function getErrorTemplateRegistry(IContainer $container) : ErrorTemplateRegistry
    {
        return new ErrorTemplateRegistry();
    }

    /**
     * Gets the rule extension registry
     *
     * @param IContainer $container The IoC container
     * @return RuleExtensionRegistry The rule extension registry
     */
    protected function getRuleExtensionRegistry(IContainer $container) : RuleExtensionRegistry
    {
        return new RuleExtensionRegistry();
    }

    /**
     * Gets the rules factory
     *
     * @param IContainer $container The IoC container
     * @return RulesFactory The rules factory
     */
    protected function getRulesFactory(IContainer $container) : RulesFactory
    {
        return new RulesFactory(
            $this->ruleExtensionRegistry,
            $this->errorTemplateRegistry,
            $this->errorTemplateCompiler
        );
    }

    /**
     * Gets the validator factory
     *
     * @param IContainer $container The IoC container
     * @return IValidatorFactory The validator factory
     */
    protected function getValidatorFactory(IContainer $container) : IValidatorFactory
    {
        return new ValidatorFactory($this->rulesFactory);
    }

    /**
     * Registers any custom rule extensions
     *
     * @param RuleExtensionRegistry $ruleExtensionRegistry The registry to register rules to
     */
    protected function registerRuleExtensions(RuleExtensionRegistry $ruleExtensionRegistry)
    {
        // Let extending classes override this
    }
}
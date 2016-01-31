<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Cryptography;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Cryptography\Encryption\Encrypter;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\BcryptHasher;
use Opulence\Cryptography\Hashing\IHasher;
use Opulence\Cryptography\Utilities\Strings;
use Opulence\Ioc\IContainer;

/**
 * Defines the cryptography bootstrapper
 */
class CryptographyBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [IEncrypter::class, IHasher::class, Strings::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $strings = $this->getStringUtility();
        $container->bind(IEncrypter::class, $this->getEncrypter($strings));
        $container->bind(IHasher::class, $this->getHasher($strings));
        $container->bind(Strings::class, $strings);
    }

    /**
     * Gets the encrypter to use
     *
     * @param Strings $strings The string utility
     * @return IEncrypter The encrypter
     */
    protected function getEncrypter(Strings $strings) : IEncrypter
    {
        return new Encrypter($this->environment->getVar("ENCRYPTION_KEY"), $strings);
    }

    /**
     * Gets the hasher to use
     *
     * @param Strings $strings The string utility
     * @return IHasher The hasher to use
     */
    protected function getHasher(Strings $strings) : IHasher
    {
        return new BcryptHasher($strings);
    }

    /**
     * Gets the string utility to use
     *
     * @return Strings The string utility
     */
    protected function getStringUtility() : Strings
    {
        return new Strings();
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the cryptography bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\Cryptography;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Cryptography\Encryption\Encrypter;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Hashing\BcryptHasher;
use Opulence\Cryptography\Hashing\IHasher;
use Opulence\Cryptography\Utilities\Strings;
use Opulence\IoC\IContainer;

class Cryptography extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
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
    protected function getEncrypter(Strings $strings)
    {
        return new Encrypter($this->environment->getVar("ENCRYPTION_KEY"), $strings);
    }

    /**
     * Gets the hasher to use
     *
     * @param Strings $strings The string utility
     * @return IHasher The hasher to use
     */
    protected function getHasher(Strings $strings)
    {
        return new BcryptHasher($strings);
    }

    /**
     * Gets the string utility to use
     *
     * @return Strings The string utility
     */
    protected function getStringUtility()
    {
        return new Strings();
    }
}
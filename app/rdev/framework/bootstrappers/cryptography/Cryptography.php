<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the cryptography bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Cryptography;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Cryptography\Encryption\Encrypter;
use RDev\Cryptography\Encryption\IEncrypter;
use RDev\Cryptography\Hashing\BcryptHasher;
use RDev\Cryptography\Hashing\IHasher;
use RDev\Cryptography\Utilities\Strings;
use RDev\IoC\IContainer;

class Cryptography extends Bootstrapper
{
    /**
     * {@inheritdoc}
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
        return new Encrypter($this->environment->getVariable("ENCRYPTION_KEY"), $strings);
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
<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the cryptography bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Cryptography;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Cryptography\Encryption\Encrypter;
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
        $container->bind("RDev\\Cryptography\\Encryption\\IEncrypter", $this->getEncrypter($strings));
        $container->bind("RDev\\Cryptography\\Hashing\\IHasher", $this->getHasher($strings));
        $container->bind("RDev\\Cryptography\\Utilities\\Strings", $strings);
    }

    /**
     * Gets the encrypter to use
     *
     * @param Strings $strings The string utility
     * @return Encrypter The encrypter
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
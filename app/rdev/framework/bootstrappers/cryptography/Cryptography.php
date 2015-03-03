<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the cryptography bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Cryptography;
use RDev\Applications\Bootstrappers;
use RDev\Cryptography\Encryption;
use RDev\Cryptography\Hashing;
use RDev\Cryptography\Utilities;
use RDev\IoC;

class Cryptography extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $strings = $this->getStringUtility();
        $container->bind("RDev\\Cryptography\\Encryption\\IEncrypter", $this->getEncrypter($strings));
        $container->bind("RDev\\Cryptography\\Hashing\\IHasher", $this->getHasher($strings));
        $container->bind("RDev\\Cryptography\\Utilities\\Strings", $strings);
    }

    /**
     * Gets the encrypter to use
     *
     * @param Utilities\Strings $strings The string utility
     * @return Encryption\Encrypter The encrypter
     */
    protected function getEncrypter(Utilities\Strings $strings)
    {
        return new Encryption\Encrypter($this->environment->getVariable("ENCRYPTION_KEY"), $strings);
    }

    /**
     * Gets the hasher to use
     *
     * @param Utilities\Strings $strings The string utility
     * @return Hashing\IHasher The hasher to use
     */
    protected function getHasher(Utilities\Strings $strings)
    {
        return new Hashing\BcryptHasher($strings);
    }

    /**
     * Gets the string utility to use
     *
     * @return Utilities\Strings The string utility
     */
    protected function getStringUtility()
    {
        return new Utilities\Strings();
    }
}
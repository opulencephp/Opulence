<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Cryptography\Bootstrappers;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Cryptography\Encryption\Encrypter;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Cryptography\Encryption\Keys\Key;
use Opulence\Cryptography\Hashing\BcryptHasher;
use Opulence\Cryptography\Hashing\IHasher;
use Opulence\Ioc\IContainer;
use RuntimeException;

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
        return [IEncrypter::class, IHasher::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindInstance(IEncrypter::class, $this->getEncrypter());
        $container->bindInstance(IHasher::class, $this->getHasher());
    }

    /**
     * Gets the encrypter to use
     *
     * @return IEncrypter The encrypter
     * @throws RuntimeException Thrown if the encryption key is not set
     */
    protected function getEncrypter() : IEncrypter
    {
        $encryptionKey = $this->environment->getVar("ENCRYPTION_KEY");

        if ($encryptionKey === null) {
            throw new RuntimeException("\"ENCRYPTION_KEY\" value not set in environment.  Check that you have it set in an environment config file such as \".env.app.php\".  Note:  \".env.example.php\" is only a template for environment config files - it is not actually used.");
        }
        
        if (mb_strlen($encryptionKey, "8bit") < 32) {
            throw new RuntimeException("The minimum length encryption key has been upgraded from 16 bytes to 32 bytes.  Please re-run \"php apex encryption:generatekey\" to create a new, suitably-long encryption key.");
        }

        return new Encrypter(new Key($encryptionKey));
    }

    /**
     * Gets the hasher to use
     *
     * @return IHasher The hasher to use
     */
    protected function getHasher() : IHasher
    {
        return new BcryptHasher();
    }
}
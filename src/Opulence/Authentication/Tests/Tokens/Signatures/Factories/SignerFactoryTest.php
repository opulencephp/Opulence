<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\Signatures\Factories;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\Factories\SignerFactory;
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use Opulence\Authentication\Tokens\Signatures\RsaSsaPkcsSigner;

/**
 * Tests the signer factory
 */
class SignerFactoryTest extends \PHPUnit\Framework\TestCase
{
    private SignerFactory $factory;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->factory = new SignerFactory();
    }

    /**
     * Tests creating asymmetric signers
     */
    public function testCreatingAsymmetricSigners(): void
    {
        $algorithms = [Algorithms::RSA_SHA256, Algorithms::RSA_SHA384, Algorithms::RSA_SHA512];

        foreach ($algorithms as $algorithm) {
            $signer = $this->factory->createSigner($algorithm, 'public', 'private');
            $this->assertInstanceOf(RsaSsaPkcsSigner::class, $signer);
            $this->assertEquals($algorithm, $signer->getAlgorithm());
        }
    }

    /**
     * Tests creating symmetric signers
     */
    public function testCreatingSymmetricSigners(): void
    {
        $algorithms = [Algorithms::SHA256, Algorithms::SHA384, Algorithms::SHA512];

        foreach ($algorithms as $algorithm) {
            $signer = $this->factory->createSigner($algorithm, 'public');
            $this->assertInstanceOf(HmacSigner::class, $signer);
            $this->assertEquals($algorithm, $signer->getAlgorithm());
        }
    }

    /**
     * Tests that an exception is thrown when no private key is specified for an asymmetric algorithm
     */
    public function testExceptionThrownWhenNoPrivateKeySpecifiedForAsymmetricAlgorithm(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->createSigner(Algorithms::RSA_SHA256, 'public');
    }

    /**
     * Tests that an exception is thrown when the public key is in the incorrect format
     */
    public function testExceptionThrownWhenPublicKeyIsInIncorrectFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->createSigner(Algorithms::SHA256, ['foo']);
    }
}

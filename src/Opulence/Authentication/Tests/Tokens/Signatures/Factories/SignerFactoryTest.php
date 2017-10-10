<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

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
    /** @var SignerFactory The factory to use in tests */
    private $factory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->factory = new SignerFactory();
    }

    /**
     * Tests creating asymmetric signers
     */
    public function testCreatingAsymmetricSigners()
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
    public function testCreatingSymmetricSigners()
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
    public function testExceptionThrownWhenNoPrivateKeySpecifiedForAsymmetricAlgorithm()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->createSigner(Algorithms::RSA_SHA256, 'public');
    }

    /**
     * Tests that an exception is thrown when the public key is in the incorrect format
     */
    public function testExceptionThrownWhenPublicKeyIsInIncorrectFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->createSigner(Algorithms::SHA256, ['foo']);
    }
}

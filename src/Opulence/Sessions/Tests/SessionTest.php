<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Sessions\Tests;

use InvalidArgumentException;
use Opulence\Sessions\Ids\Generators\IdGenerator;
use Opulence\Sessions\Ids\Generators\IIdGenerator;
use Opulence\Sessions\Session;

/**
 * Tests the session class
 */
class SessionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests aging flashed data
     */
    public function testAgingFlashData()
    {
        $session = new Session();
        $session->flash('foo', 'bar');
        $this->assertEquals(
            [
                'foo' => 'bar',
                $session::NEW_FLASH_KEYS_KEY => ['foo'],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
        $session->ageFlashData();
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertTrue($session->has('foo'));
        $this->assertEquals(
            [
                'foo' => 'bar',
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => ['foo']
            ],
            $session->getAll()
        );
        $session->flash('baz', 'blah');
        $session->ageFlashData();
        $this->assertNull($session->get('foo'));
        $this->assertFalse($session->has('foo'));
        $this->assertEquals('blah', $session->get('baz'));
        $this->assertEquals(
            [
                'baz' => 'blah',
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => ['baz']
            ],
            $session->getAll()
        );
        $this->assertTrue($session->has('baz'));
        $session->ageFlashData();
        $this->assertNull($session->get('baz'));
        $this->assertFalse($session->has('baz'));
        $this->assertEquals(
            [
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
    }

    /**
     * Tests that writing to flash data after aging it will reflash it
     */
    public function testAgingFlashDataAndWritingToItAgain()
    {
        $session = new Session();
        $session->flash('foo', 'bar');
        $session->ageFlashData();
        $session->flash('foo', 'baz');
        $session->ageFlashData();
        $this->assertTrue($session->has('foo'));
        $this->assertEquals('baz', $session->get('foo'));
        $this->assertEquals(
            [
                'foo' => 'baz',
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => ['foo']
            ],
            $session->getAll()
        );
        $session->ageFlashData();
        $this->assertFalse($session->has('foo'));
        $this->assertNull($session->get('foo'));
        $this->assertEquals(
            [
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
    }

    /**
     * Tests checking if an offset exists
     */
    public function testCheckingIfOffsetExists()
    {
        $session = new Session();
        $session['foo'] = 'bar';
        $this->assertTrue(isset($session['foo']));
        $this->assertTrue($session->has('foo'));
        $this->assertFalse(isset($session['bar']));
        $this->assertFalse($session->has('bar'));
    }

    /**
     * Tests deleting a variable
     */
    public function testDeletingVariable()
    {
        $session = new Session();
        $session->set('foo', 'bar');
        $session->delete('foo');
        $this->assertFalse($session->has('foo'));
        $this->assertEquals([], $session->getAll());
    }

    /**
     * Tests flashing data and getting it
     */
    public function testFlashingDataAndGettingIt()
    {
        $session = new Session();
        $session->flash('foo', 'bar');
        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(
            [
                'foo' => 'bar',
                $session::NEW_FLASH_KEYS_KEY => ['foo'],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
    }

    /**
     * Tests flushing the session
     */
    public function testFlushing()
    {
        $session = new Session();
        $session['foo'] = 'bar';
        $session->flush();
        $this->assertFalse(isset($session['foo']));
        $this->assertEquals([], $session->getAll());
    }

    /**
     * Tests getting all session variables
     */
    public function testGettingAll()
    {
        $session = new Session();
        $session->set('foo', 'bar');
        $session->set('baz', 'blah');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $session->getAll());
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $id = str_repeat(1, IIdGenerator::MIN_LENGTH);
        $idGenerator = $this->createMock(IIdGenerator::class);
        $idGenerator->expects($this->any())->method('idIsValid')->willReturn(true);
        $session = new Session($id, $idGenerator);
        $this->assertEquals($id, $session->getId());
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $session = new Session();
        $this->assertNull($session['non-existent']);
        $this->assertNull($session->get('non-existent'));
    }

    /**
     * Tests getting a non-existent variable with a default value
     */
    public function testGettingNonExistentVariableWithDefaultValue()
    {
        $session = new Session();
        $this->assertEquals('bar', $session->get('foo', 'bar'));
    }

    /**
     * Tests reflashing data
     */
    public function testReflashing()
    {
        $session = new Session();
        $session->flash('foo', 'bar');
        $session->ageFlashData();
        $session->reflash();
        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(
            [
                'foo' => 'bar',
                $session::NEW_FLASH_KEYS_KEY => ['foo'],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
        $session->ageFlashData();
        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(
            [
                'foo' => 'bar',
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => ['foo']
            ],
            $session->getAll()
        );
        $session->ageFlashData();
        $this->assertFalse($session->has('foo'));
        $this->assertNull($session->get('foo'));
        $this->assertEquals(
            [
                $session::NEW_FLASH_KEYS_KEY => [],
                $session::STALE_FLASH_KEYS_KEY => []
            ],
            $session->getAll()
        );
    }

    /**
     * Tests regenerating the session Id
     */
    public function testRegenerateId()
    {
        $generatedId = str_repeat(1, IIdGenerator::MIN_LENGTH);
        $idGenerator = $this->createMock(IIdGenerator::class);
        $idGenerator->expects($this->at(0))->method('idIsValid')->willReturn(false);
        $idGenerator->expects($this->at(2))->method('idIsValid')->willReturn(true);
        $idGenerator->expects($this->at(4))->method('idIsValid')->willReturn(true);
        $idGenerator->expects($this->any())->method('generate')->willReturn($generatedId);
        $session = new Session(null, $idGenerator);
        $session->regenerateId();
        $this->assertEquals($generatedId, $session->getId());
    }

    /**
     * Tests regenerating the session Id with the default generator
     */
    public function testRegeneratingIdWithDefaultIdGenerator()
    {
        $session = new Session();
        $session->regenerateId();
        $this->assertTrue(is_string($session->getId()));
        $this->assertEquals(IdGenerator::DEFAULT_LENGTH, strlen($session->getId()));
    }

    /**
     * Tests setting and getting the name
     */
    public function testSettingAndGettingName()
    {
        $session = new Session();
        $session->setName('foo');
        $this->assertEquals('foo', $session->getName());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $constructorId = str_repeat(1, IIdGenerator::MIN_LENGTH);
        $idGenerator = $this->createMock(IIdGenerator::class);
        $idGenerator->expects($this->any())->method('idIsValid')->willReturn(true);
        $session = new Session($constructorId, $idGenerator);
        $setterId = str_repeat(2, IIdGenerator::MIN_LENGTH);
        $session->setId($setterId);
        $this->assertEquals($setterId, $session->getId());
    }

    /**
     * Tests that invalid Id causes a new Id to be generated
     */
    public function testSettingInvalidIdCausesNewIdToBeGenerated()
    {
        $idGenerator = $this->createMock(IIdGenerator::class);
        $idGenerator->expects($this->at(0))->method('idIsValid')->willReturn(false);
        $idGenerator->expects($this->at(2))->method('idIsValid')->willReturn(true);
        $idGenerator->expects($this->at(3))->method('idIsValid')->willReturn(false);
        $idGenerator->expects($this->at(5))->method('idIsValid')->willReturn(true);
        $idGenerator->expects($this->any())->method('generate')->willReturn(str_repeat(1, IIdGenerator::MIN_LENGTH));
        $session = new Session(1, $idGenerator);
        $this->assertNotEquals(1, $session->getId());
        $session->setId(2);
        $this->assertNotEquals(2, $session->getId());
    }

    /**
     * Tests setting many variables
     */
    public function testSettingMany()
    {
        $session = new Session();
        $session->set('foo', 'bar');
        $session->setMany(['baz' => 'blah']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'blah'], $session->getAll());
        $session->setMany(['foo' => 'somethingnew']);
        $this->assertEquals(['foo' => 'somethingnew', 'baz' => 'blah'], $session->getAll());
    }

    /**
     * Tests setting a null offset
     */
    public function testSettingNullOffset()
    {
        $this->expectException(InvalidArgumentException::class);
        $session = new Session();
        $session[] = 'foo';
    }

    /**
     * Tests setting the offset
     */
    public function testSettingOffset()
    {
        $session = new Session();
        $session['foo'] = 'bar';
        $this->assertEquals('bar', $session['foo']);
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(['foo' => 'bar'], $session->getAll());
    }

    /**
     * Tests setting a variable
     */
    public function testSettingVariable()
    {
        $session = new Session();
        $session->set('foo', 'bar');
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(['foo' => 'bar'], $session->getAll());
    }

    /**
     * Tests starting a session
     */
    public function testStarting()
    {
        $variables = ['foo' => 'bar', 'baz' => 'blah'];
        $session = new Session();
        $session->start($variables);
        $this->assertEquals('bar', $session['foo']);
        $this->assertEquals('blah', $session['baz']);
        $this->assertTrue($session->hasStarted());
    }

    /**
     * Tests that a session is not marked as started before it's started
     */
    public function testThatSessionNotMarkedAsStartedBeforeStarting()
    {
        $session = new Session();
        $this->assertFalse($session->hasStarted());
    }

    /**
     * Tests that an unset name is an empty string
     */
    public function testUnsetNameIsEmptyString()
    {
        $session = new Session();
        $this->assertEquals('', $session->getName());
    }

    /**
     * Tests unsetting an offset
     */
    public function testUnsettingOffset()
    {
        $session = new Session();
        $session['foo'] = 'bar';
        unset($session['foo']);
        $this->assertNull($session['foo']);
        $this->assertEquals([], $session->getAll());
    }
}

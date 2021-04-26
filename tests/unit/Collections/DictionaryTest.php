<?php declare(strict_types=1);
namespace NekoLib\Tests\Unit\Collections;

use InvalidArgumentException;
use NekoLib\Collections\Dictionary;
use NekoLib\Collections\KeyNotFoundException;
use PHPUnit\Framework\TestCase;

final class DictionaryTest extends TestCase
{
    public function testEmpty(): Dictionary
    {
        $map = new Dictionary();
        $this->assertTrue($map->isEmpty());
        $this->assertEquals(0, $map->count());
        return $map;
    }

    /**
     * @depends testEmpty
     */
    public function testAdd(Dictionary $map): Dictionary
    {
        $map->add('foo', 'bar');
        $this->assertFalse($map->isEmpty());
        $this->assertEquals(1, $map->count());
        $this->assertEquals('bar', $map->get('foo'));
        return $map;
    }

    /**
     * @depends testAdd
     */
    public function testAddThrowsExceptionIfTheKeyExists(Dictionary $map): void
    {
        $this->expectException(InvalidArgumentException::class);
        $map->add('foo', 'baz');
    }

    /**
     * @depends testAdd
     */
    public function testSet(Dictionary $map): Dictionary
    {
        // Update key
        $map->set('foo', 'baz');
        $this->assertEquals('baz', $map->get('foo'));

        $map->set('wakipai', 'Watame');
        $this->assertEquals('Watame', $map->get('wakipai'));
        $this->assertEquals(2, $map->count());
        return $map;
    }

    /**
     * @depends testSet
     */
    public function testGetThrowsExceptionIfTheKeyIsNotFound(Dictionary $map): void
    {
        $this->expectException(KeyNotFoundException::class);
        $map->get('unknown key');
    }

    /**
     * @depends testSet
     */
    public function testContainsKey_(Dictionary $map): void
    {
        $this->assertTrue($map->containsKey('wakipai'));
        $this->assertFalse($map->containsKey('unknown key'));
    }
}

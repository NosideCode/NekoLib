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
        $this->assertSame(0, $map->count());
        return $map;
    }

    /**
     * @depends testEmpty
     */
    public function testAdd(Dictionary $map): Dictionary
    {
        $map->add('foo', 'bar');
        $this->assertFalse($map->isEmpty());
        $this->assertSame(1, $map->count());
        $this->assertSame('bar', $map->get('foo'));
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
        $this->assertSame('baz', $map->get('foo'));

        $map->set('wakipai', 'Watame');
        $this->assertSame('Watame', $map->get('wakipai'));
        $this->assertSame(2, $map->count());
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
    public function testContainsKey_(Dictionary $map): Dictionary
    {
        $this->assertTrue($map->containsKey('wakipai'));
        $this->assertFalse($map->containsKey('unknown key'));
        return $map;
    }

    /**
     * @depends testContainsKey_
     */
    public function testContainsValue_(Dictionary $map): Dictionary
    {
        $this->assertTrue($map->containsValue('Watame'));
        $this->assertFalse($map->containsValue('Yagoo'));
        return $map;
    }

    /**
     * @depends testContainsValue_
     */
    public function testRemove(Dictionary $map): void
    {
        $this->assertSame(2, $map->count());

        // Key must exists first
        $map->get('foo');

        // Remove 1 key
        $map->remove('foo');
        $this->assertSame(1, $map->count());

        $this->expectException(KeyNotFoundException::class);
        $map->get('foo');
    }
}

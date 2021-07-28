<?php declare(strict_types=1);
namespace Neko\Tests\Unit\Collections;

use Neko\Collections\ArrayList;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use function range;

final class ArrayListTest extends TestCase
{
    public function testEmpty(): ArrayList
    {
        $list = new ArrayList();
        $this->assertTrue($list->isEmpty());
        $this->assertSame(0, $list->count());
        return $list;
    }

    /**
     * @depends testEmpty
     *
     */
    public function testAdd(ArrayList $list): ArrayList
    {
        $list->add('Watame');
        $list->add('Pekora');
        $list->add('Botan');

        $this->assertFalse($list->isEmpty());
        $this->assertSame(3, $list->count());
        $this->assertSame('Watame', $list->get(0));
        $this->assertSame('Pekora', $list->get(1));
        $this->assertSame('Botan', $list->get(2));
        return $list;
    }

    /**
     * @depends testAdd
     */
    public function testSet(ArrayList $original): void
    {
        // Clone list
        $list = new ArrayList($original);
        $list->set(1, 'Foo');
        $this->assertSame('Foo', $list->get(1));
    }

    /**
     * @depends testAdd
     */
    public function testInsertFirst(ArrayList $list): ArrayList
    {
        $list->insert(0, 'Elite Miko');
        $this->assertSame(4, $list->count());
        $this->assertSame('Elite Miko', $list->get(0));
        $this->assertSame('Watame', $list->get(1));
        $this->assertSame('Pekora', $list->get(2));
        $this->assertSame('Botan', $list->get(3));
        return $list;
    }

    /**
     * @depends testInsertFirst
     */
    public function testInsertBetween(ArrayList $list): ArrayList
    {
        $list->insert(2, 'Yubi Yubi');
        $this->assertSame(5, $list->count());
        $this->assertSame('Elite Miko', $list->get(0));
        $this->assertSame('Watame', $list->get(1));
        $this->assertSame('Yubi Yubi', $list->get(2));
        $this->assertSame('Pekora', $list->get(3));
        $this->assertSame('Botan', $list->get(4));
        return $list;
    }

    /**
     * @depends testInsertBetween
     */
    public function testInsertLast(ArrayList $list): void
    {
        $list->insert(5, 'Gura');
        $this->assertSame(6, $list->count());
        $this->assertSame('Gura', $list->get(5));
    }

    /**
     * @return ArrayList
     *
     * addRange() method is a wrapper around insertRange().
     */
    public function testInsertRange_First(): ArrayList
    {
        $list = new ArrayList(['A', 'B', 'C']);
        $list->insertRange(0, ['X', 'Y', 'Z']); // [X, Y, Z, A, B, C]
        $this->assertSame(6, $list->count());
        $this->assertSame('X', $list->get(0));
        $this->assertSame('Y', $list->get(1));
        $this->assertSame('Z', $list->get(2));
        $this->assertSame('A', $list->get(3));
        $this->assertSame('B', $list->get(4));
        $this->assertSame('C', $list->get(5));
        return $list;
    }

    /**
     * @depends testInsertRange_First
     */
    public function testInsertRange_Between(ArrayList $list): ArrayList
    {
        $list->insertRange(3, [1, 2, 3]); // [X, Y, Z, 1, 2, 3, A, B, C]
        $this->assertSame(9, $list->count());
        $this->assertSame('X', $list->get(0));
        $this->assertSame('Y', $list->get(1));
        $this->assertSame('Z', $list->get(2));
        $this->assertSame(1, $list->get(3));
        $this->assertSame(2, $list->get(4));
        $this->assertSame(3, $list->get(5));
        $this->assertSame('A', $list->get(6));
        $this->assertSame('B', $list->get(7));
        $this->assertSame('C', $list->get(8));
        return $list;
    }

    /**
     * @depends testInsertRange_Between
     */
    public function testInsertRange_Last(ArrayList $list): ArrayList
    {
        $list->insertRange(9, [4, 5, 6]); // [X, Y, Z, 1, 2, 3, A, B, C, 4, 5, 6]
        $this->assertSame(12, $list->count());
        $this->assertSame(4, $list->get(9));
        $this->assertSame(5, $list->get(10));
        $this->assertSame(6, $list->get(11));
        return $list;
    }

    /**
     * @depends testInsertRange_Last
     */
    public function testRemoveAt_(ArrayList $list): ArrayList
    {
        // 3 is indexed in 5:   0  1  2  3  4  5  6  7  8  9 10 11
        $list->removeAt(5); // [X, Y, Z, 1, 2, 3, A, B, C, 4, 5, 6]
        $this->assertSame(11, $list->count());
        $this->assertSame('A', $list->get(5));
        $this->assertSame('B', $list->get(6));
        $this->assertSame('C', $list->get(7));
        $this->assertSame(4, $list->get(8));
        $this->assertSame(5, $list->get(9));
        $this->assertSame(6, $list->get(10));
        return $list;
    }

    /**
     * @depends testRemoveAt_
     */
    public function testRemoveRange_First(ArrayList $list): ArrayList
    {
        // Re-insert the 3 removed in previous test
        $list->insert(5, 3);

        // Remove X, Y, Z from the list
        $list->removeRange(0, 3); // [1, 2, 3, A, B, C, 4, 5, 6]
        $this->assertSame(9, $list->count());
        $this->assertSame(1, $list->get(0));
        $this->assertSame(2, $list->get(1));
        $this->assertSame(3, $list->get(2));
        $this->assertSame('A', $list->get(3));
        $this->assertSame('B', $list->get(4));
        $this->assertSame('C', $list->get(5));
        $this->assertSame(4, $list->get(6));
        $this->assertSame(5, $list->get(7));
        $this->assertSame(6, $list->get(8));
        return $list;
    }

    /**
     * @depends testRemoveRange_First
     */
    public function testRemoveRange_Between(ArrayList $list): ArrayList
    {
        // Remove A, B, C from the list
        $list->removeRange(3, 3); // [1, 2, 3, 4, 5, 6]
        $this->assertSame(6, $list->count());
        $this->assertSame(1, $list->get(0));
        $this->assertSame(2, $list->get(1));
        $this->assertSame(3, $list->get(2));
        $this->assertSame(4, $list->get(3));
        $this->assertSame(5, $list->get(4));
        $this->assertSame(6, $list->get(5));
        return $list;
    }

    /**
     * @depends testRemoveRange_Between
     */
    public function testRemoveRange_Last(ArrayList $list): void
    {
        // Remove last 3 integers
        $list->removeRange(3, 3); // [1, 2, 3]
        $this->assertSame(3, $list->count());
    }

    public function testRemoveAll_(): void
    {
        $list = new ArrayList(range(1, 10));

        $this->assertSame(10, $list->count());

        // Remove even values
        $list->removeAll(fn($value) => $value % 2 === 0);
        $this->assertSame(5, $list->count());

        $this->assertSame(1, $list->get(0));
        $this->assertSame(3, $list->get(1));
        $this->assertSame(5, $list->get(2));
        $this->assertSame(7, $list->get(3));
        $this->assertSame(9, $list->get(4));

        // Remove remaining values
        $list->removeAll(fn() => true);
        $this->assertTrue($list->isEmpty());
    }
}

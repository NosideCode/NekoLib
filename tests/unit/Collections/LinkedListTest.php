<?php declare(strict_types=1);
namespace Neko\Tests\Unit\Collections;

use Neko\Collections\LinkedList;
use Neko\Collections\LinkedListNode;
use PHPUnit\Framework\TestCase;

final class LinkedListTest extends TestCase
{
    public function testEmpty(): LinkedList
    {
        $list = new LinkedList();
        $this->assertTrue($list->isEmpty());
        $this->assertSame(0, $list->count());
        return $list;
    }

    /**
     * @depends testEmpty
     */
    public function testAddFirst_(LinkedList $list): LinkedList
    {
        $list->addFirst('A');
        $head = $list->getFirst();
        $tail = $list->getLast();

        $this->assertSame(1, $list->count());
        $this->assertSame($head, $tail);
        return $list;
    }

    /**
     * @depends testAddFirst_
     */
    public function testAddLast_(LinkedList $list): LinkedList
    {
        $list->addLast('Z');
        $head = $list->getFirst();
        $tail = $list->getLast();

        $this->assertSame(2, $list->count());
        $this->assertNotSame($head, $tail);
        $this->assertSame('Z', $tail->getValue());
        return $list;
    }

    /**
     * @depends testAddLast_
     */
    public function testAddAfter_(LinkedList $list): LinkedListNode
    {
        $ref = $list->getFirst();
        $next = $ref->getNext();
        $node = $list->addAfter($ref, 'C');

        $this->assertSame(3, $list->count());
        $this->assertSame($list->getFirst(), $node->getPrevious());
        $this->assertSame($list->getLast(), $node->getNext());
        $this->assertSame($ref->getNext(), $node);
        return $node;
    }

    /**
     * @depends testAddAfter_
     */
    public function testAddBefore_(LinkedListNode $node): LinkedList
    {
        $list = $node->getList();
        $prev = $node->getPrevious();
        $newNode = $list->addBefore($node, 'B');

        $this->assertSame(4, $list->count());
        $this->assertSame($prev, $newNode->getPrevious());
        $this->assertSame($node, $newNode->getNext());
        $this->assertSame($newNode, $node->getPrevious());
        return $list;
    }

    /**
     * @depends testAddBefore_
     */
    public function testRemove_(LinkedList $list): LinkedList
    {
        $node = $list->find('C');
        $prev = $node->getPrevious();
        $next = $node->getNext();
        $this->assertTrue($list->remove('C'));

        $this->assertSame(3, $list->count());
        $this->assertSame($next, $prev->getNext());
        $this->assertSame($prev, $next->getPrevious());
        return $list;
    }

    /**
     * @depends testRemove_
     */
    public function testRemoveFirst_(LinkedList $list): LinkedList
    {
        $first = $list->getFirst();
        $next = $first->getNext();
        $list->removeFirst();

        $this->assertSame(2, $list->count());
        $this->assertSame($next, $list->getFirst());
        $this->assertNull($list->getFirst()->getPrevious());
        return $list;
    }

    /**
     * @depends testRemoveFirst_
     */
    public function testRemoveLast_(LinkedList $list): LinkedList
    {
        $last = $list->getLast();
        $prev = $last->getPrevious();
        $list->removeLast();

        $this->assertSame(1, $list->count());
        $this->assertSame($prev, $list->getLast());
        $this->assertNull($list->getLast()->getNext());
        return $list;
    }

    /**
     * @depends testRemoveLast_
     */
    public function testSingleNode_(LinkedList $list): void
    {
        $this->assertSame($list->getFirst(), $list->getLast());

        $node = $list->getFirst();
        $this->assertNull($node->getNext());
        $this->assertNull($node->getPrevious());
    }
}

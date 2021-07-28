<?php declare(strict_types=1);
namespace Neko\Tests\Unit\Collections;

use Neko\Collections\Stack;
use PHPUnit\Framework\TestCase;

final class StackTest extends TestCase
{
    public function testEmpty(): Stack
    {
        $stack = new Stack();
        $this->assertSame(0, $stack->count());
        $this->assertTrue($stack->isEmpty());
        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(Stack $stack): Stack
    {
        $value = 'Watame';
        $stack->push($value);
        $this->assertSame($value, $stack->peek());
        $this->assertSame(1, $stack->count());
        $this->assertFalse($stack->isEmpty());
        return $stack;
    }

    /**
     * @depends testPush
     */
    public function testPop(Stack $stack): void
    {
        $this->assertSame('Watame', $stack->pop());
        $this->assertSame(0, $stack->count());
        $this->assertTrue($stack->isEmpty());
    }

    public function testTryPeek_FailsWhenEmpty(): void
    {
        $stack = new Stack();
        $this->assertFalse($stack->tryPeek($result));
        $this->assertNull($result);
    }

    public function testTryPop_FilsWhenEmpty(): void
    {
        $stack = new Stack();
        $this->assertFalse($stack->tryPop($result));
        $this->assertNull($result);
    }
}

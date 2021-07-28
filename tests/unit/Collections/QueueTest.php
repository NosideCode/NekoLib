<?php declare(strict_types=1);
namespace Neko\Tests\Unit\Collections;

use Neko\Collections\Queue;
use PHPUnit\Framework\TestCase;

final class QueueTest extends TestCase
{
    public function testEmpty(): Queue
    {
        $queue = new Queue();
        $this->assertSame(0, $queue->count());
        $this->assertTrue($queue->isEmpty());
        return $queue;
    }

    /**
     * @depends testEmpty
     */
    public function testEnqueue(Queue $queue): Queue
    {
        $value = 'Pekora';
        $queue->enqueue($value);
        $this->assertSame($value, $queue->peek());
        $this->assertSame(1, $queue->count());
        $this->assertFalse($queue->isEmpty());
        return $queue;
    }

    /**
     * @depends testEnqueue
     */
    public function testDequeue(Queue $queue): void
    {
        $this->assertSame('Pekora', $queue->dequeue());
        $this->assertSame(0, $queue->count());
        $this->assertTrue($queue->isEmpty());
    }

    public function testTryPeek_FailsWhenEmpty(): void
    {
        $queue = new Queue();
        $this->assertFalse($queue->tryPeek($result));
        $this->assertNull($result);
    }

    public function testTryDequeue_FailsWhenEmpty(): void
    {
        $queue = new Queue();
        $this->assertFalse($queue->tryDequeue($result));
        $this->assertNull($result);
    }
}

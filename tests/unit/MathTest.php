<?php declare(strict_types=1);
namespace NekoLib\Tests\Unit;

use InvalidArgumentException;
use NekoLib\Math;
use OverflowException;
use PHPUnit\Framework\TestCase;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class MathTest extends TestCase
{
    public function testClampReturnsValue(): void
    {
        $min = 50;
        $max = 100;
        $val = 60;
        $this->assertSame($val, Math::clamp($val, $min, $max));
    }

    public function testClampReturnsMin(): void
    {
        $min = 50;
        $max = 100;
        $val = 18;
        $this->assertSame($min, Math::clamp($val, $min, $max));
    }

    public function testClampReturnsMax(): void
    {
        $min = 50;
        $max = 100;
        $val = 200;
        $this->assertSame($max, Math::clamp($val, $min, $max));
    }

    public function testClampThrowsExceptionIfMinIsGreaterThanMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $min = 100;
        $max = 20;
        $val = 50;
        Math::clamp($val, $min, $max);
    }

    public function testDigitsOfWithPositiveValues(): void
    {
        $val = 200;
        $digits = 3;
        $this->assertSame($digits, Math::digitsOf($val));
    }

    public function testDigitsOfWithNegativeValues(): void
    {
        $val = -30;
        $digits = 2;
        $this->assertSame($digits, Math::digitsOf($val));
    }

    public function testFactorial(): void
    {
        $base = 7;
        $expected = 5040;
        $this->assertSame($expected, Math::factorial($base));
    }

    public function testFactorialThrowsExceptionWhenNIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Math::factorial(-3);
    }

    public function testDivRemFunction(): void
    {
        $a = 12;
        $b = 3;
        $result = Math::divRem($a, $b, $remainder);

        $this->assertEquals($a / $b, $result);
        $this->assertEquals($a % $b, $remainder);
    }

    public function testTryParseIntSucceedsWithCorrectValues(): void
    {
        $input = '204';
        $expected = 204;
        $parsed = Math::tryParseInt($input, $result);

        $this->assertTrue($parsed);
        $this->assertSame($expected, $result);
    }

    public function testTryParseIntFailsWithIncorrectValues(): void
    {
        $input = '123abc';
        $parsed = Math::tryParseInt($input, $result);
        $this->assertFalse($parsed);
    }

    public function testTryParseFloatSucceedsWithCorrectValues(): void
    {
        $input = '3.14';
        $expected = 3.14;
        $parsed = Math::tryParseFloat($input, $result);

        $this->assertTrue($parsed);
        $this->assertSame($expected, $result);
    }

    public function testTryParseFloatFailsWithIncorrectValues(): void
    {
        $input = '123abc.456';
        $parsed = Math::tryParseFloat($input, $result);
        $this->assertFalse($parsed);
    }

    public function testOverflowIsDetectedWhenAdding(): void
    {
        $this->expectException(OverflowException::class);
        $a = PHP_INT_MAX;
        $b = 1;
        Math::checkedAdd($a, $b);
    }

    public function testOverflowIsDetectedWhenSubtracting(): void
    {
        $this->expectException(OverflowException::class);
        $a = PHP_INT_MIN;
        $b = 1;
        Math::checkedSubtract($a, $b);
    }

    public function testTryToDetectOverflowWhenAdding(): void
    {
        $a = PHP_INT_MAX;
        $b = 1;
        $overflows = Math::tryCheckedAdd($a, $b, $result);
        $this->assertFalse($overflows);
    }

    public function testTryToDetectOverflowWhenSubtracting(): void
    {
        $a = PHP_INT_MIN;
        $b = 1;
        $overflows = Math::tryCheckedSubtract($a, $b, $result);
        $this->assertFalse($overflows);
    }
}

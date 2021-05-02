<?php declare(strict_types=1);
namespace NekoLib\Tests\Unit\IO;

use NekoLib\IO\Path;
use PHPUnit\Framework\TestCase;
use function implode;
use const DIRECTORY_SEPARATOR;

final class PathTest extends TestCase
{
    private string $testDirectoryPath;
    private string $testFilePath;

    public function setUp(): void
    {
        $components = ['C:', 'User', 'Watame', 'Downloads'];
        $this->testDirectoryPath = implode(DIRECTORY_SEPARATOR, $components);

        $components[] = 'Example.png';
        $this->testFilePath = implode(DIRECTORY_SEPARATOR, $components);
    }

    public function testEndsWithDirectorySeparator_(): void
    {
        $dirPath = $this->testDirectoryPath . DIRECTORY_SEPARATOR;
        $this->assertTrue(Path::endsInDirectorySeparator($dirPath));
        $this->assertFalse(Path::endsInDirectorySeparator($this->testFilePath));
    }

    public function testGetDirectoryName_(): void
    {
        $this->assertSame($this->testDirectoryPath, Path::getDirectoryName($this->testFilePath));
    }

    public function testGetFileName_(): void
    {
        $this->assertSame('Example.png', Path::getFileName($this->testFilePath));
    }

    public function testGetFileNameWithoutExtension_(): void
    {
        $this->assertSame('Example', Path::getFileNameWithoutExtension($this->testFilePath));
    }

    public function testHasExtension_(): void
    {
        $this->assertTrue(Path::hasExtension($this->testFilePath));
        $this->assertTrue(Path::hasExtension('.gitignore'));
        $this->assertFalse(Path::hasExtension($this->testDirectoryPath));
        $this->assertFalse(Path::hasExtension('TrickyFileName.'));
    }

    public function testGetExtension_(): void
    {
        $this->assertSame('.png', Path::getExtension($this->testFilePath));
        $this->assertSame('', Path::getExtension($this->testDirectoryPath));
    }

    public function testChangeExtension_(): void
    {
        $this->assertSame('Moona.hey', Path::changeExtension('Moona.peko', 'hey'));
        $this->assertSame('Botan.poi', Path::changeExtension('Botan.yeet', '.poi'));
        $this->assertSame('Pekora.kon.peko', Path::changeExtension('Pekora.kon.kon', '.peko'));
    }

    public function testNullRemovesExtension(): void
    {
        $this->assertSame('Marine', Path::changeExtension('Marine.nothorny', null));
        $this->assertSame('A.chan', Path::changeExtension('A.chan.kun', null));
        $this->assertSame('Wakipai', Path::changeExtension('Wakipai', null));
    }

    public function testIsPathRooted_(): void
    {
        $this->assertTrue(Path::isPathRooted($this->testFilePath));
        $this->assertFalse(Path::isPathRooted('./relative/path'));
    }

    public function testGetPathRoot(): void
    {
        $this->assertSame('C:\\', Path::getPathRoot($this->testFilePath));
        $this->assertSame('/', Path::getPathRoot('/home/user/foo/bar.sh'));
    }

    public function testCombine(): void
    {
        $expected = implode(DIRECTORY_SEPARATOR, ['/FOO', 'BAR', 'BAZ.txt']);
        $this->assertSame($expected, Path::combine('/FOO', 'BAR', 'BAZ.txt'));

        $expected = implode(DIRECTORY_SEPARATOR, ['/ROOT', 'FOO', 'BAR']);
        $this->assertSame($expected, Path::combine('NonRoot', 'Ignore Pls', '/ROOT', 'FOO', 'BAR'));
    }
}

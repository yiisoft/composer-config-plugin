<?php

namespace Yiisoft\Composer\Config\Tests\Unit\Configs;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Readers\EnvReader;
use Yiisoft\Composer\Config\Readers\JsonReader;
use Yiisoft\Composer\Config\Readers\PhpReader;
use Yiisoft\Composer\Config\Readers\ReaderFactory;
use Yiisoft\Composer\Config\Readers\YamlReader;

/**
 * ReaderFactoryTest.
 */
class ReaderFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate(): void
    {
        $this->checkGet('.env', EnvReader::class);
        $this->checkGet('.json', JsonReader::class);
        $yml = $this->checkGet('.yml', YamlReader::class);
        $yaml = $this->checkGet('.yaml', YamlReader::class);
        $php = $this->checkGet('.php', PhpReader::class);
        $php2 = $this->checkGet('.php', PhpReader::class);

        $this->assertNotSame($php, $php2);
        $this->assertNotSame($yml, $yaml);
    }

    public function checkGet(string $name, string $class)
    {
        $builder = $this->createMock(Builder::class);
        $reader = ReaderFactory::get($builder, $name);
        $this->assertInstanceOf($class, $reader);

        return $reader;
    }
}

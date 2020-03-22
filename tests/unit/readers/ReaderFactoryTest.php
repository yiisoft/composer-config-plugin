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
    protected $builder;

    public function testCreate()
    {
        $this->builder = new Builder();

        $env    = $this->checkGet('.env', EnvReader::class);
        $json   = $this->checkGet('.json', JsonReader::class);
        $yml    = $this->checkGet('.yml', YamlReader::class);
        $yaml   = $this->checkGet('.yaml', YamlReader::class);
        $php    = $this->checkGet('.php', PhpReader::class);
        $php2   = $this->checkGet('.php', PhpReader::class);

        $this->assertSame($php, $php2);
        $this->assertSame($yml, $yaml);
    }

    public function checkGet(string $name, string $class)
    {
        $reader = ReaderFactory::get($this->builder, $name);
        $this->assertInstanceOf($class, $reader);
        $this->assertSame($this->builder, $reader->getBuilder());

        return $reader;
    }
}

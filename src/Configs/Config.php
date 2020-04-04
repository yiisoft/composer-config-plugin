<?php

namespace Yiisoft\Composer\Config\Configs;

use ReflectionException;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exceptions\FailedWriteException;
use Yiisoft\Composer\Config\Readers\ReaderFactory;
use Yiisoft\Composer\Config\Utils\Helper;

/**
 * Config class represents output configuration file.
 */
class Config
{
    private const UNIX_DS = '/';
    private const BASE_DIR_MARKER = '<<<base-dir>>>';

    /**
     * @var string config name
     */
    protected $name;

    /**
     * @var array sources - paths to config source files
     */
    protected $sources = [];

    /**
     * @var array config value
     */
    protected $values = [];

    /**
     * @var Builder
     */
    protected $builder;

    public function __construct(Builder $builder, string $name)
    {
        $this->builder = $builder;
        $this->name = $name;
    }

    public function clone(Builder $builder): self
    {
        $config = new Config($builder, $this->name);
        $config->sources = $this->sources;
        $config->values = $this->values;

        return $config;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function load(array $paths = []): self
    {
        $this->sources = $this->loadFiles($paths);

        return $this;
    }

    protected function loadFiles(array $paths): array
    {
        switch (count($paths)) {
        case 0:
            return [];
        case 1:
            return [$this->loadFile(reset($paths))];
        }

        $configs = [];
        foreach ($paths as $path) {
            $cs = $this->loadFiles($this->glob($path));
            foreach ($cs as $config) {
                if (!empty($config)) {
                    $configs[] = $config;
                }
            }
        }

        return $configs;
    }

    protected function glob(string $path): array
    {
        if (strpos($path, '*') === false) {
            return [$path];
        }

        return glob($path);
    }

    /**
     * Reads config file.
     * @param string $path
     * @return array configuration read from file
     */
    protected function loadFile($path): array
    {
        $reader = ReaderFactory::get($this->builder, $path);

        return $reader->read($path);
    }

    /**
     * Merges given configs and writes at given name.
     * @return Config
     */
    public function build(): self
    {
        $this->values = $this->calcValues($this->sources);

        return $this;
    }

    public function write(): self
    {
        $this->writeFile($this->getOutputPath(), $this->values);

        return $this;
    }

    protected function calcValues(array $sources): array
    {
        $values = call_user_func_array([Helper::class, 'mergeConfig'], $sources);
        $values = Helper::fixConfig($values);

        return $this->substituteOutputDirs($values);
    }

    protected function writeFile(string $path, array $data): void
    {
        $this->writePhpFile($path, $data);
    }

    /**
     * Writes complete PHP config file by full path.
     * @param string $path
     * @param string|array $data
     * @param bool $withEnv
     * @param bool $withDefines
     * @throws FailedWriteException
     * @throws ReflectionException
     */
    protected function writePhpFile(string $path, $data): void
    {
        $depth = $this->findDepth();
        $baseDir = $depth>0 ? "dirname(__DIR__, $depth)" : '__DIR__';
        static::putFile($path, $this->replaceMarkers(implode("\n\n", array_filter([
            'header'  => '<?php',
            'baseDir' => "\$baseDir = $baseDir;",
            'BASEDIR' => "defined('COMPOSER_CONFIG_PLUGIN_BASEDIR') or define('COMPOSER_CONFIG_PLUGIN_BASEDIR', \$baseDir);",
            'dotenv'  => $this->withEnv() ? "\$_ENV = array_merge((array) require __DIR__ . '/dotenv.php', (array) \$_ENV);" : '',
            'defines' => $this->withDefines() ? $this->builder->getConfig('defines')->buildRequires() : '',
            'params'  => $this->withParams() ? "\$params = require __DIR__ . '/params.php';" : '',
            'content' => is_array($data) ? $this->renderVars($data) : $data,
        ]))) . "\n");
    }

    private function withEnv(): bool
    {
        return !in_array(static::class, [System::class, DotEnv::class], true);
    }

    private function withDefines(): bool
    {
        return !in_array(static::class, [System::class, DotEnv::class, Defines::class], true);
    }

    private function withParams(): bool
    {
        return !in_array(static::class, [System::class, DotEnv::class, Defines::class, Params::class], true);
    }

    private function findDepth(): int
    {
        $outDir = dirname(self::normalizePath($this->getOutputPath()));
        $diff = substr($outDir, strlen($this->getBaseDir()));

        return substr_count($diff, self::UNIX_DS);
    }

    /**
     * @param array $vars array to be exported
     * @return string
     * @throws ReflectionException
     */
    protected function renderVars(array $vars): string
    {
        return 'return ' . Helper::exportVar($vars) . ';';
    }

    protected function replaceMarkers(string $content): string
    {
        $content = str_replace("'" . self::BASE_DIR_MARKER, "\$baseDir . '", $content);

        return str_replace("'?" . self::BASE_DIR_MARKER, "'?' . \$baseDir . '", $content);
    }

    /**
     * Writes file if content changed.
     * @param string $path
     * @param string $content
     * @throws FailedWriteException
     */
    protected static function putFile($path, $content): void
    {
        if (file_exists($path) && $content === file_get_contents($path)) {
            return;
        }
        $dirname = dirname($path);
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true) && !is_dir($dirname)) {
            throw new FailedWriteException(sprintf('Directory "%s" was not created', $dirname));
        }
        if (false === file_put_contents($path, $content)) {
            throw new FailedWriteException("Failed write file $path");
        }
    }

    /**
     * Substitute output paths in given data array recursively with marker.
     * @param array $data
     * @return array
     */
    public function substituteOutputDirs(array $data): array
    {
        $dir = static::normalizePath($this->getBaseDir());

        return static::substitutePaths($data, $dir, self::BASE_DIR_MARKER);
    }

    /**
     * Normalizes given path with given directory separator.
     * Default forced to Unix directory separator for substitutePaths to work properly in Windows.
     * @param string $path path to be normalized
     * @param string $ds directory separator
     * @return string
     */
    public static function normalizePath($path, $ds = self::UNIX_DS): string
    {
        return rtrim(strtr($path, '/\\', $ds . $ds), $ds);
    }

    /**
     * Substitute all paths in given array recursively with alias if applicable.
     * @param array $data
     * @param string $dir
     * @param string $alias
     * @return array
     */
    public static function substitutePaths($data, $dir, $alias): array
    {
        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = static::substitutePath($value, $dir, $alias);
            } elseif (is_array($value)) {
                $value = static::substitutePaths($value, $dir, $alias);
            }
        }

        return $data;
    }

    /**
     * Substitute path with alias if applicable.
     * @param string $path
     * @param string $dir
     * @param string $alias
     * @return string
     */
    protected static function substitutePath($path, $dir, $alias): string
    {
        $end = $dir . self::UNIX_DS;
        $skippable = 0 === strncmp($path, '?', 1) ? '?' : '';
        if ($skippable) {
            $path = substr($path, 1);
        }
        if ($path === $dir) {
            $result = $alias;
        } elseif (strpos($path, $end) === 0) {
            $result = $alias . substr($path, strlen($end) - 1);
        } else {
            $result = $path;
        }

        return $skippable . $result;
    }

    public function getBaseDir(): string
    {
        return dirname(__DIR__, 5);
    }

    public function getOutputPath(string $name = null): string
    {
        return $this->builder->getOutputPath($name ?: $this->name);
    }
}

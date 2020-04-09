<?php

namespace Yiisoft\Composer\Config\Configs;

use ReflectionException;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\ContentWriter;
use Yiisoft\Composer\Config\Readers\ReaderFactory;
use Yiisoft\Composer\Config\Utils\Helper;

/**
 * Config class represents output configuration file.
 */
class Config
{
    private const BASE_DIR_MARKER = '<<<base-dir>>>';

    /**
     * @var string config name
     */
    private string $name;

    /**
     * @var array sources - paths to config source files
     */
    private array $sources = [];

    /**
     * @var array config value
     */
    protected array $values = [];

    protected Builder $builder;

    protected ContentWriter $contentWriter;

    public function __construct(Builder $builder, string $name)
    {
        $this->builder = $builder;
        $this->name = $name;
        $this->contentWriter = new ContentWriter();
    }

    public function clone(Builder $builder): self
    {
        $config = new Config($builder, $this->name);
        $config->sources = $this->sources;
        $config->values = $this->values;

        return $config;
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

    private function loadFiles(array $paths): array
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

    private function glob(string $path): array
    {
        if (strpos($path, '*') === false) {
            return [$path];
        }

        return glob($path);
    }

    /**
     * Reads config file.
     *
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
     *
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
        $helper = new Helper();
        $values = $helper->mergeConfig(...$sources);
        $values = $helper->fixConfig($values);

        return $this->substituteOutputDirs($values);
    }

    protected function writeFile(string $path, array $data): void
    {
        $depth = $this->findDepth();
        $baseDir = $depth > 0 ? "dirname(__DIR__, $depth)" : '__DIR__';

        $content = $this->replaceMarkers(implode("\n\n", array_filter([
            'header' => '<?php',
            'baseDir' => "\$baseDir = $baseDir;",
            'dotenv' => $this->hasEnv()
                ? "\$_ENV = array_merge((array) require __DIR__ . '/dotenv.php', (array) \$_ENV);" : '',
            'defines' => $this->hasConstants() ? $this->builder->getConfig('defines')->buildRequires() : '',
            'params' => $this->hasParams() ? "\$params = require __DIR__ . '/params.php';" : '',
            'content' => $this->renderVars($data),
        ])));
        $this->contentWriter->write($path, $content . "\n");
    }

    public function hasEnv(): bool
    {
        return false;
    }

    public function hasConstants(): bool
    {
        return false;
    }

    public function hasParams(): bool
    {
        return false;
    }

    private function findDepth(): int
    {
        $outDir = dirname($this->normalizePath($this->getOutputPath()));
        $diff = substr($outDir, strlen($this->getBaseDir()));

        return substr_count($diff, '/');
    }

    /**
     * @param array $vars array to be exported
     * @return string
     * @throws ReflectionException
     */
    private function renderVars(array $vars): string
    {
        $helper = new Helper();

        return 'return ' . $helper->exportVar($vars) . ';';
    }

    private function replaceMarkers(string $content): string
    {
        return str_replace(
            ["'" . self::BASE_DIR_MARKER, "'?" . self::BASE_DIR_MARKER],
            ["\$baseDir . '", "'?' . \$baseDir . '"],
            $content
        );
    }

    /**
     * Substitute output paths in given data array recursively with marker.
     *
     * @param array $data
     * @return array
     */
    protected function substituteOutputDirs(array $data): array
    {
        $dir = $this->normalizePath($this->getBaseDir());

        return $this->substitutePaths($data, $dir, self::BASE_DIR_MARKER);
    }

    /**
     * Normalizes given path with given directory separator.
     * Default forced to Unix directory separator for substitutePaths to work properly in Windows.
     *
     * @param string $path path to be normalized
     * @param string $ds directory separator
     * @return string
     */
    private function normalizePath($path): string
    {
        return rtrim(strtr($path, '/\\', '//'), '/');
    }

    /**
     * Substitute all paths in given array recursively with alias if applicable.
     *
     * @param array $data
     * @param string $dir
     * @param string $alias
     * @return array
     */
    private function substitutePaths($data, $dir, $alias): array
    {
        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = $this->substitutePath($value, $dir, $alias);
            } elseif (is_array($value)) {
                $value = $this->substitutePaths($value, $dir, $alias);
            }
        }

        return $data;
    }

    /**
     * Substitute path with alias if applicable.
     *
     * @param string $path
     * @param string $dir
     * @param string $alias
     * @return string
     */
    private function substitutePath($path, $dir, $alias): string
    {
        $end = $dir . '/';
        $skippable = 0 === strncmp($path, '?', 1);
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

        return ($skippable ? '?' : '') . $result;
    }

    private function getBaseDir(): string
    {
        return dirname(__DIR__, 5);
    }

    protected function getOutputPath(string $name = null): string
    {
        return $this->builder->getOutputPath($name ?: $this->name);
    }
}

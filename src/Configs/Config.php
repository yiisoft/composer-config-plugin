<?php

namespace Yiisoft\Composer\Config\Configs;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\ContentWriter;
use Yiisoft\Composer\Config\Readers\ReaderFactory;
use Yiisoft\Composer\Config\Utils\Helper;
use Yiisoft\Composer\Config\Utils\PathHelper;

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
        $values = ArrayHelper::merge(...$sources);

        return $this->substituteOutputDirs($values);
    }

    protected function writeFile(string $path, array $data): void
    {
        $depth = $this->findDepth();
        $baseDir = $depth > 0 ? "dirname(__DIR__, $depth)" : '__DIR__';

        $params = $this->paramsRequired() ? "(array) require __DIR__ . '/params.php'" : '[]';
        $constants = $this->constantsRequired() ? $this->builder->getConfig('constants')->buildRequires() : '';
        $envs = $this->envsRequired() ? "\$_ENV = array_merge((array) require __DIR__ . '/envs.php', \$_ENV);" : '';
        $variables = Helper::exportVar($data);

        $content = <<<PHP
<?php
\$baseDir = {$baseDir};
\$params = {$params};
{$constants}
{$envs}
return {$variables};
PHP;

        $this->contentWriter->write($path, $this->replaceMarkers($content) . "\n");
    }

    public function envsRequired(): bool
    {
        return true;
    }

    public function constantsRequired(): bool
    {
        return true;
    }

    public function paramsRequired(): bool
    {
        return true;
    }

    private function findDepth(): int
    {
        $outDir = PathHelper::realpath(dirname($this->getOutputPath()));
        $diff = substr($outDir, strlen(PathHelper::realpath($this->getBaseDir())));

        return substr_count($diff, '/');
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
        $dir = PathHelper::normalize($this->getBaseDir());

        return $this->substitutePaths($data, $dir, self::BASE_DIR_MARKER);
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
        return $this->builder->getBaseDir();
    }

    protected function getOutputPath(string $name = null): string
    {
        return $this->builder->getOutputPath($name ?: $this->name);
    }
}

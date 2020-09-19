<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\ContentWriter;
use Yiisoft\Composer\Config\Reader\ReaderFactory;
use Yiisoft\Composer\Config\Util\Helper;
use Yiisoft\Composer\Config\Util\PathHelper;

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
    protected function loadFile(string $path): array
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
        $envs = $this->envsRequired() ? "\$_ENV = array_merge((array) require __DIR__ . '/envs.php', \$_ENV);" : '';
        $constants = $this->constantsRequired() ? $this->builder->getConfig('constants')->buildRequires() : '';
        $params = $this->paramsRequired() ? "\$params = (array) require __DIR__ . '/params.php';" : '';
        $uses = '';
        if($data) {
            $variables = $this->buildPhpPartials($data);
            $uses = implode("\n", $this->builder->uses);
        } else {
            $variables = '[]';
        }

        $content = <<<PHP
<?php

{$uses}

\$baseDir = {$baseDir};

{$envs}

{$constants}

{$params}

return {$variables};
PHP;

        $this->contentWriter->write($path, $this->replaceMarkers($content) . "\n");
    }

    /**
     * Build PHP partials
     * @param $data
     *
     * @return string
     */
    protected function buildPhpPartials($data)
    {
        $output = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $output .= "'" . $key . "' => " . $this->buildPhpPartials($value) . ",\n";
            } elseif(is_string($value)) {
                if(isset($this->builder->closures["'{$value}'"])) {
                    $value = $this->builder->closures["'{$value}'"];
                } else {
                    $value = "'{$value}'";
                }
                $output .= "'" . $key . "' => {$value},\n";
            } else {
                if(is_bool($value)) {
                    $value = $value? 'true': 'false';
                    $output .= "'" . $key . "' => {$value},\n";
                } elseif(is_null($value)){
                    $output .= "'" . $key . "' => null,\n";
                } else {
                    $output .= "'" . $key . "' => {$value},\n";
                }
            }
        }

        if($output) {
            while (preg_match('~\'__(\w+)__\'~', $output, $m)) {
                foreach ($this->builder->closures as $closureKey => $closure) {
                    if (strpos($output, $closureKey) !== false) {
                        $output = str_replace($closureKey, $closure, $output);
                    }
                }
            }
        }

        return "[$output]";
    }

    protected function envsRequired(): bool
    {
        return true;
    }

    protected function constantsRequired(): bool
    {
        return true;
    }

    protected function paramsRequired(): bool
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

        return $this->substitutePaths($data, $dir);
    }

    /**
     * Substitute all paths in given array recursively with marker if applicable.
     *
     * @param array $data
     * @param string $dir
     * @return array
     */
    private function substitutePaths($data, $dir): array
    {
        $res = [];
        foreach ($data as $key => $value) {
            $res[$this->substitutePath($key, $dir)] = $this->substitutePath($value, $dir);
        }

        return $res;
    }

    /**
     * Substitute all paths in given value if applicable.
     *
     * @param mixed $value
     * @param string $dir
     * @return mixed
     */
    private function substitutePath($value, $dir)
    {
        if (is_string($value)) {
            return $this->substitutePathInString($value, $dir);
        }
        if (is_array($value)) {
            return $this->substitutePaths($value, $dir);
        }

        return $value;
    }

    /**
     * Substitute path with marker in string if applicable.
     *
     * @param string $path
     * @param string $dir
     * @return string
     */
    private function substitutePathInString($path, $dir): string
    {
        $end = $dir . '/';
        $skippable = 0 === strncmp($path, '?', 1);
        if ($skippable) {
            $path = substr($path, 1);
        }
        if ($path === $dir) {
            $result = self::BASE_DIR_MARKER;
        } elseif (strpos($path, $end) === 0) {
            $result = self::BASE_DIR_MARKER . substr($path, strlen($end) - 1);
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

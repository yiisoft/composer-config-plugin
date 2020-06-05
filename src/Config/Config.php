<?php

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
        return ArrayHelper::merge(...$sources);
    }

    protected function writeFile(string $path, array $data): void
    {
        $depth = $this->findDepth();
        $baseDir = $depth > 0 ? "dirname(__DIR__, $depth)" : '__DIR__';

        $envs = $this->envsRequired() ? "\$_ENV = array_merge((array) require __DIR__ . '/envs.php', \$_ENV);" : '';
        $constants = $this->constantsRequired() ? $this->builder->getConfig('constants')->buildRequires() : '';
        $params = $this->paramsRequired() ? "\$params = (array) require __DIR__ . '/params.php';" : '';
        $variables = Helper::exportVar($data);

        $content = <<<PHP
<?php

\$baseDir = {$baseDir};

{$envs}

{$constants}

{$params}

return {$variables};
PHP;

        $this->contentWriter->write($path, $content . "\n");
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

    private function getBaseDir(): string
    {
        return $this->builder->getBaseDir();
    }

    protected function getOutputPath(string $name = null): string
    {
        return $this->builder->getOutputPath($name ?: $this->name);
    }
}

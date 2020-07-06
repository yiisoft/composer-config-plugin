<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use JsonException;
use Yiisoft\Composer\Config\Config\Config;
use Yiisoft\Composer\Config\Config\ConfigFactory;
use Yiisoft\Composer\Config\Util\Resolver;

use function dirname;

/**
 * Builder assembles config files.
 */
class Builder
{
    private const OUTPUT_DIR_SUFFIX = '-output';

    /**
     * @var string path to the Composer project root
     */
    private string $baseDir;

    /**
     * @var string path to output assembled configs
     */
    private string $outputDir;

    /**
     * @var Config[] configurations
     */
    private array $configs = [];

    private ConfigFactory $configFactory;

    /**
     * Builder constructor.
     *
     * @param ConfigFactory $configFactory
     * @param string $baseDir path to the Composer project root
     */
    public function __construct(ConfigFactory $configFactory, string $baseDir)
    {
        $this->configFactory = $configFactory;
        $this->baseDir = $baseDir;
        $this->outputDir = self::findOutputDir($baseDir);
    }

    public function createAlternative($name): Builder
    {
        $alt = new static($this->configFactory, $this->baseDir);
        $alt->setOutputDir($this->outputDir . DIRECTORY_SEPARATOR . $name);
        foreach (['aliases', 'packages'] as $key) {
            $alt->configs[$key] = $this->getConfig($key)->clone($alt);
        }

        return $alt;
    }

    public function setOutputDir(?string $outputDir): void
    {
        $this->outputDir = $outputDir
            ? static::buildAbsPath($this->getBaseDir(), $outputDir)
            : static::findOutputDir($this->getBaseDir());
    }

    public static function rebuild(?string $baseDir = null): void
    {
        // Ensure COMPOSER_HOME is set in case web server does not give PHP OS environment variables
        if (!(getenv('APPDATA') || getenv('HOME') || getenv('COMPOSER_HOME'))) {
            $path = sys_get_temp_dir() . '/.composer';
            if (!is_dir($path) && !mkdir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
            putenv('COMPOSER_HOME=' . $path);
        }

        Plugin::buildAllConfigs($baseDir ?? self::findBaseDir());
    }

    /**
     * Returns default output dir.
     *
     * @param string|null $baseDir path to the root Composer package. When `null`,
     * @return string
     * @throws JsonException
     */
    private static function findOutputDir(string $baseDir = null): string
    {
        if ($baseDir === null) {
            $baseDir = static::findBaseDir();
        }
        $path = $baseDir . DIRECTORY_SEPARATOR . 'composer.json';
        $data = @json_decode(file_get_contents($path), true);
        $dir = $data['extra'][Package::EXTRA_OUTPUT_DIR_OPTION_NAME] ?? null;

        return $dir ? static::buildAbsPath($baseDir, $dir) : static::defaultOutputDir($baseDir);
    }

    private static function findBaseDir(): string
    {
        $candidates = [
            // normal relative path
            dirname(__DIR__, 4),
            // console
            getcwd(),
            // symlinked web
            dirname(getcwd())
        ];

        foreach ($candidates as $baseDir) {
            if (file_exists($baseDir . DIRECTORY_SEPARATOR . 'composer.json')) {
                return $baseDir;
            }
        }

        throw new \RuntimeException('Cannot find directory that contains composer.json');
    }

    /**
     * Returns default output dir.
     *
     * @param string $baseDir path to base directory
     * @return string
     */
    private static function defaultOutputDir(string $baseDir = null): string
    {
        if ($baseDir) {
            $dir = $baseDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'yiisoft' . DIRECTORY_SEPARATOR . basename(dirname(__DIR__));
        } else {
            $dir = dirname(__DIR__);
        }

        return $dir . static::OUTPUT_DIR_SUFFIX;
    }

    /**
     * Returns full path to assembled config file.
     *
     * @param string $filename name of config
     * @param string $baseDir path to base dir
     * @return string absolute path
     * @throws JsonException
     */
    public static function path(string $filename, string $baseDir = null): string
    {
        return static::buildAbsPath(static::findOutputDir($baseDir), $filename . '.php');
    }

    private static function buildAbsPath(string $dir, string $file): string
    {
        return self::isAbsolutePath($file) ? $file : $dir . DIRECTORY_SEPARATOR . $file;
    }

    private static function isAbsolutePath(string $path): bool
    {
        return strpos($path, '/') === 0 || strpos($path, ':') === 1 || strpos($path, '\\\\') === 0;
    }

    /**
     * Builds all (user and system) configs by given files list.
     *
     * @param null|array $files files to process: config name => list of files
     */
    public function buildAllConfigs(array $files): void
    {
        $this->buildUserConfigs($files);
        $this->buildSystemConfigs();
    }

    /**
     * Builds configs by given files list.
     *
     * @param null|array $files files to process: config name => list of files
     * @return array
     */
    private function buildUserConfigs(array $files): array
    {
        $resolver = new Resolver($files);
        $files = $resolver->get();
        foreach ($files as $name => $paths) {
            $this->getConfig($name)->load($paths)->build()->write();
        }

        return $files;
    }

    private function buildSystemConfigs(): void
    {
        foreach (['aliases', 'packages'] as $name) {
            $this->getConfig($name)->build()->write();
        }
    }

    public function getOutputPath(string $name): string
    {
        return $this->outputDir . DIRECTORY_SEPARATOR . $name . '.php';
    }

    public function getConfig(string $name)
    {
        if (!array_key_exists($name, $this->configs)) {
            $this->configs[$name] = $this->configFactory->create($this, $name);
        }

        return $this->configs[$name];
    }

    public function getVars(): array
    {
        $vars = [];
        foreach ($this->configs as $name => $config) {
            $vars[$name] = $config->getValues();
        }

        return $vars;
    }

    public function mergeAliases(array $aliases): void
    {
        $this->getConfig('aliases')->mergeValues($aliases);
    }

    public function setPackage(string $name, array $data): void
    {
        $this->getConfig('packages')->setValue($name, $data);
    }

    /**
     * @return string a full path to the project root
     */
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }
}

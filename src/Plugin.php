<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Yiisoft\Composer\Config\Config\ConfigFactory;
use Yiisoft\Composer\Config\Exception\BadConfigurationException;
use Yiisoft\Composer\Config\Exception\FailedReadException;
use Yiisoft\Composer\Config\Package\AliasesCollector;
use Yiisoft\Composer\Config\Package\PackageFinder;
use Yiisoft\Composer\Config\Reader\ReaderFactory;
use Dotenv\Dotenv;

final class Plugin
{
    /**
     * @var Package[] the array of active composer packages
     */
    private array $packages;

    private array $alternatives = [];

    private ?Package $rootPackage = null;

    /**
     * @var array config name => list of files
     * Important: defines config files processing order:
     * envs then constants then params then other configs
     */
    private array $files = [
        'envs' => [],
        'constants' => [],
        'params' => [],
    ];

    /**
     * @var array package name => configs as listed in `composer.json`
     */
    private array $originalFiles = [];

    private Builder $builder;

    /**
     * @var IOInterface
     */
    private IOInterface $io;

    private AliasesCollector $aliasesCollector;

    /**
     * Initializes the plugin object with the passed $composer and $io.
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function __construct(Composer $composer, IOInterface $io)
    {
        $baseDir = dirname($composer->getConfig()->get('vendor-dir')) . DIRECTORY_SEPARATOR;
        $this->builder = new Builder(new ConfigFactory(), realpath($baseDir));
        $this->aliasesCollector = new AliasesCollector(new Filesystem());
        $this->io = $io;
        $this->collectPackages($composer);
    }

    public static function buildAllConfigs(string $projectRootPath): void
    {
        $factory = new \Composer\Factory();
        $output = $factory::createOutput();
        $input = new \Symfony\Component\Console\Input\ArgvInput([]);
        $helperSet = new \Symfony\Component\Console\Helper\HelperSet();
        $io = new \Composer\IO\ConsoleIO($input, $output, $helperSet);
        $composer = $factory->createComposer($io, $projectRootPath . '/composer.json', true, $projectRootPath, false);
        $plugin = new self($composer, $io);
        $plugin->build();
    }

    public function build(): void
    {
        $this->io->overwriteError('<info>Assembling config files</info>');

        $this->scanPackages();
        $this->reorderFiles();

        $this->builder->buildAllConfigs($this->files);

        $saveFiles = $this->files;
        $saveEnv = $_ENV;
        foreach ($this->alternatives as $name => $files) {
            $this->files = $saveFiles;
            $_ENV = $saveEnv;
            $builder = $this->builder->createAlternative($name);
            $this->addFiles($this->rootPackage, $files);
            $builder->buildAllConfigs($this->files);
        }
    }

    private function scanPackages(): void
    {
        foreach ($this->packages as $package) {
            if ($package->isComplete()) {
                $this->processPackage($package);
            }
        }
    }

    private function reorderFiles(): void
    {
        foreach (array_keys($this->files) as $name) {
            $this->files[$name] = $this->getAllFiles($name);
        }
        foreach ($this->files as $name => $files) {
            $this->files[$name] = $this->orderFiles($files);
        }
    }

    private function getAllFiles(string $name, array $stack = []): array
    {
        if (empty($this->files[$name])) {
            return [];
        }
        $res = [];
        foreach ($this->files[$name] as $file) {
            if (strncmp($file, '$', 1) === 0) {
                if (!in_array($name, $stack, true)) {
                    $res = array_merge($res, $this->getAllFiles(substr($file, 1), array_merge($stack, [$name])));
                }
            } else {
                $res[] = $file;
            }
        }

        return $res;
    }

    private function orderFiles(array $files): array
    {
        if ($files === []) {
            return [];
        }
        $keys = array_combine($files, $files);
        $res = [];
        foreach ($this->orderedFiles as $file) {
            if (array_key_exists($file, $keys)) {
                $res[$file] = $file;
            }
        }

        return array_values($res);
    }

    /**
     * Scans the given package and collects packages data.
     *
     * @param Package $package
     */
    private function processPackage(Package $package): void
    {
        $files = $package->getFiles();
        $this->originalFiles[$package->getPrettyName()] = $files;

        if (!empty($files)) {
            $this->addFiles($package, $files);
        }
        if ($package->isRoot()) {
            $this->rootPackage = $package;
            $this->loadDotEnv($package);
            $devFiles = $package->getDevFiles();
            if (!empty($devFiles)) {
                $this->addFiles($package, $devFiles);
            }
            $alternatives = $package->getAlternatives();
            if (is_string($alternatives)) {
                $this->alternatives = $this->readConfig($package, $alternatives);
            } elseif (is_array($alternatives)) {
                $this->alternatives = $alternatives;
            } elseif (!empty($alternatives)) {
                throw new BadConfigurationException('Alternatives must be array or path to configuration file.');
            }
        }

        $aliases = $this->aliasesCollector->collect($package);

        $this->builder->mergeAliases($aliases);
        $this->builder->setPackage($package->getPrettyName(), array_filter([
            'name' => $package->getPrettyName(),
            'version' => $package->getVersion(),
            'reference' => $package->getSourceReference() ?: $package->getDistReference(),
            'aliases' => $aliases,
        ]));
    }

    private function readConfig($package, $file): array
    {
        $path = $package->preparePath($file);
        if (!file_exists($path)) {
            throw new FailedReadException("failed read file: $file");
        }
        $reader = ReaderFactory::get($this->builder, $path);

        return $reader->read($path);
    }

    private function loadDotEnv(Package $package): void
    {
        $path = $package->preparePath('.env');
        if (file_exists($path) && class_exists(Dotenv::class)) {
            $this->addFile($package, 'envs', $path);
        }
    }

    /**
     * Adds given files to the list of files to be processed.
     * Prepares `constants` in reversed order (outer package first) because
     * constants cannot be redefined.
     *
     * @param Package $package
     * @param array $files
     */
    private function addFiles(Package $package, array $files): void
    {
        foreach ($files as $name => $paths) {
            $paths = (array) $paths;
            if ('constants' === $name) {
                $paths = array_reverse($paths);
            }
            foreach ($paths as $path) {
                $this->addFile($package, $name, $path);
            }
        }
    }

    private array $orderedFiles = [];

    private function addFile(Package $package, string $name, string $path): void
    {
        $path = $package->preparePath($path);
        if (!array_key_exists($name, $this->files)) {
            $this->files[$name] = [];
        }
        if (in_array($path, $this->files[$name], true)) {
            return;
        }
        if ('constants' === $name) {
            array_unshift($this->orderedFiles, $path);
            array_unshift($this->files[$name], $path);
        } else {
            $this->orderedFiles[] = $path;
            $this->files[$name][] = $path;
        }
    }

    private function collectPackages(Composer $composer): void
    {
        $vendorDir = $composer->getConfig()->get('vendor-dir');
        $rootPackage = $composer->getPackage();
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
        $packageFinder = new PackageFinder($vendorDir, $rootPackage, $packages);

        $this->packages = $packageFinder->findPackages();
    }
}

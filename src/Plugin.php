<?php

namespace Yiisoft\Composer\Config;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Yiisoft\Composer\Config\Exceptions\BadConfigurationException;
use Yiisoft\Composer\Config\Exceptions\FailedReadException;
use Yiisoft\Composer\Config\Readers\ReaderFactory;
use Composer\Util\Filesystem;
use Yiisoft\Composer\Config\Configs\ConfigFactory;
use Yiisoft\Composer\Config\Package\AliasesCollector;
use Yiisoft\Composer\Config\Package\PackageFinder;

/**
 * Plugin class.
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Package[] the array of active composer packages
     */
    private array $packages = [];

    private array $alternatives = [];

    private ?string $outputDir = null;

    private ?Package $rootPackage = null;

    /**
     * @var array config name => list of files
     */
    private array $files = [
        'dotenv' => [],
        'defines' => [],
        'params' => [],
    ];

    /**
     * @var array package name => configs as listed in `composer.json`
     */
    private array $originalFiles = [];

    /**
     * @var Builder
     */
    private Builder $builder;

    /**
     * @var Composer instance
     */
    private Composer $composer;

    /**
     * @var IOInterface
     */
    private IOInterface $io;

    private PackageFinder $packageFinder;

    private AliasesCollector $aliasesCollector;

    /**
     * Initializes the plugin object with the passed $composer and $io.
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->builder = new Builder(new ConfigFactory());
        $this->packageFinder = new PackageFinder($composer);
        $this->aliasesCollector = new AliasesCollector(new Filesystem());
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Returns list of events the plugin is subscribed to.
     * @return array list of events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => [
                ['onPostAutoloadDump', 0],
            ],
        ];
    }

    /**
     * This is the main function.
     */
    public function onPostAutoloadDump(Event $event): void
    {
        $this->io->overwriteError('<info>Assembling config files</info>');

        require_once $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';
        $this->scanPackages();
        $this->reorderFiles();

        $this->builder->setOutputDir($this->outputDir);
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
        foreach ($this->getPackages() as $package) {
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
            return[];
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
        if (empty($files)) {
            return [];
        }
        $keys = array_combine($files, $files);
        $res = [];
        foreach ($this->orderedFiles as $file) {
            if (isset($keys[$file])) {
                $res[$file] = $file;
            }
        }

        return array_values($res);
    }

    /**
     * Scans the given package and collects packages data.
     * @param Package $package
     */
    private function processPackage(Package $package)
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
            $this->outputDir = $package->getOutputDir();
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
        if (file_exists($path) && class_exists('Dotenv\Dotenv')) {
            $this->addFile($package, 'dotenv', $path);
        }
    }

    /**
     * Adds given files to the list of files to be processed.
     * Prepares `defines` in reversed order (outer package first) because
     * constants cannot be redefined.
     * @param Package $package
     * @param array $files
     */
    private function addFiles(Package $package, array $files): void
    {
        foreach ($files as $name => $paths) {
            $paths = (array) $paths;
            if ('defines' === $name) {
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
        if (!isset($this->files[$name])) {
            $this->files[$name] = [];
        }
        if (in_array($path, $this->files[$name], true)) {
            return;
        }
        if ('defines' === $name) {
            array_unshift($this->orderedFiles, $path);
            array_unshift($this->files[$name], $path);
        } else {
            $this->orderedFiles[] = $path;
            $this->files[$name][] = $path;
        }
    }

    /**
     * Gets [[packages]].
     * @return Package[]
     */
    private function getPackages(): array
    {
        if ([] === $this->packages) {
            $this->packages = $this->packageFinder->findPackages();
        }

        return $this->packages;
    }

}

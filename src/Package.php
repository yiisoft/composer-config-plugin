<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Util\Filesystem;

/**
 * Class Package.
 */
class Package
{
    public const EXTRA_FILES_OPTION_NAME = 'config-plugin';
    public const EXTRA_DEV_FILES_OPTION_NAME = 'config-plugin-dev';
    public const EXTRA_OUTPUT_DIR_OPTION_NAME = 'config-plugin-output-dir';
    public const EXTRA_ALTERNATIVES_OPTION_NAME = 'config-plugin-alternatives';

    private PackageInterface $package;

    /**
     * @var array composer.json raw data array
     */
    private array $data;

    /**
     * @var string absolute path to the root base directory
     */
    private string $baseDir;

    /**
     * @var string absolute path to vendor directory
     */
    private string $vendorDir;

    /**
     * @var Filesystem utility
     */
    private Filesystem $filesystem;

    public function __construct(PackageInterface $package, string $vendorDir)
    {
        $this->package = $package;
        $this->filesystem = new Filesystem();

        $this->vendorDir = $this->filesystem->normalizePath($vendorDir);
        $this->baseDir = dirname($this->vendorDir);
        $this->data = $this->readRawData();
    }

    /**
     * @return string package pretty name, like: vendor/name
     */
    public function getPrettyName(): string
    {
        return $this->package->getPrettyName();
    }

    /**
     * @return string package version, like: 3.0.16.0, 9999999-dev
     */
    public function getVersion(): string
    {
        return $this->package->getVersion();
    }

    /**
     * @return string package CVS revision, like: 3a4654ac9655f32888efc82fb7edf0da517d8995
     */
    public function getSourceReference(): ?string
    {
        return $this->package->getSourceReference();
    }

    /**
     * @return string package dist revision, like: 3a4654ac9655f32888efc82fb7edf0da517d8995
     */
    public function getDistReference(): ?string
    {
        return $this->package->getDistReference();
    }

    /**
     * @return bool is package complete
     */
    public function isComplete(): bool
    {
        return $this->package instanceof CompletePackageInterface;
    }

    /**
     * @return bool is this a root package
     */
    public function isRoot(): bool
    {
        return $this->package instanceof RootPackageInterface;
    }

    /**
     * @return array autoload configuration array
     */
    public function getAutoload(): array
    {
        return $this->getRawValue('autoload') ?? $this->package->getAutoload();
    }

    /**
     * @return array autoload-dev configuration array
     */
    public function getDevAutoload(): array
    {
        return $this->getRawValue('autoload-dev') ?? $this->package->getDevAutoload();
    }

    /**
     * @return array require configuration array
     */
    public function getRequires(): array
    {
        return $this->getRawValue('require') ?? $this->package->getRequires();
    }

    /**
     * @return array require-dev configuration array
     */
    public function getDevRequires(): array
    {
        return $this->getRawValue('require-dev') ?? $this->package->getDevRequires();
    }

    /**
     * @return array files array
     */
    public function getFiles(): array
    {
        return $this->getExtraValue(self::EXTRA_FILES_OPTION_NAME, []);
    }

    /**
     * @return array dev-files array
     */
    public function getDevFiles(): array
    {
        return $this->getExtraValue(self::EXTRA_DEV_FILES_OPTION_NAME, []);
    }

    /**
     * @return mixed alternatives array or path to config
     */
    public function getAlternatives()
    {
        return $this->getExtraValue(self::EXTRA_ALTERNATIVES_OPTION_NAME);
    }

    /**
     * Get extra configuration value or default
     *
     * @param string $key key to look for in extra configuration
     * @param mixed $default default to return if there's no extra configuration value
     * @return mixed extra configuration value or default
     */
    private function getExtraValue(string $key, $default = null)
    {
        return $this->getExtra()[$key] ?? $default;
    }

    /**
     * @return array extra configuration array
     */
    private function getExtra(): array
    {
        return $this->getRawValue('extra') ?? $this->package->getExtra();
    }

    /**
     * @param string $name option name
     * @return mixed raw value from composer.json if available
     */
    private function getRawValue(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * @return array composer.json contents as array
     * @throws \JsonException
     */
    private function readRawData(): array
    {
        $path = $this->preparePath('composer.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        }

        return [];
    }

    /**
     * Builds path inside of a package.
     *
     * @param string $file
     * @return string absolute paths will stay untouched
     */
    public function preparePath(string $file): string
    {
        if (0 === strncmp($file, '$', 1)) {
            return $file;
        }

        $skippable = 0 === strncmp($file, '?', 1) ? '?' : '';
        if ($skippable) {
            $file = substr($file, 1);
        }

        if (!$this->filesystem->isAbsolutePath($file)) {
            $prefix = $this->isRoot()
                ? $this->baseDir
                : $this->vendorDir . '/' . $this->getPrettyName();
            $file = $prefix . '/' . $file;
        }

        return $skippable . $this->filesystem->normalizePath($file);
    }

    public function getVendorDir(): string
    {
        return $this->vendorDir;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }
}

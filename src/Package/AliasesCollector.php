<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Package;

use Composer\Util\Filesystem;
use Yiisoft\Composer\Config\Package;

class AliasesCollector
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Collects package aliases.
     *
     * @param Package $package
     * @return array collected aliases
     */
    public function collect(Package $package): array
    {
        $aliases = array_merge(
            $this->prepareAliases($package, 'psr-0', false),
            $this->prepareAliases($package, 'psr-4', false)
        );
        if ($package->isRoot()) {
            $aliases = array_merge(
                $aliases,
                $this->prepareAliases($package, 'psr-0', true),
                $this->prepareAliases($package, 'psr-4', true)
            );
        }

        return $aliases;
    }

    /**
     * Prepare aliases.
     *
     * @param Package $package
     * @param string $psr 'psr-0' or 'psr-4'
     * @param bool $dev
     * @return array
     */
    private function prepareAliases(Package $package, string $psr, bool $dev): array
    {
        $autoload = $dev ? $package->getDevAutoload() : $package->getAutoload();
        if (empty($autoload[$psr])) {
            return [];
        }

        $aliases = [];
        foreach ($autoload[$psr] as $name => $path) {
            if (is_array($path)) {
                // ignore psr-4 autoload specifications with multiple search paths
                // we can not convert them into aliases as they are ambiguous
                continue;
            }
            $name = str_replace('\\', '/', trim($name, '\\'));
            $path = $this->preparePath($package, $path);
            if ('psr-0' === $psr) {
                $path .= '/' . $name;
            }
            $aliases["@$name"] = $path;
        }

        return $aliases;
    }

    /**
     * Builds path inside of a package.
     *
     * @param Package $package
     * @param string $file
     * @return string absolute paths will stay untouched
     */
    private function preparePath(Package $package, string $file): string
    {
        if (0 === strncmp($file, '$', 1)) {
            return $file;
        }

        $skippable = 0 === strncmp($file, '?', 1) ? '?' : '';
        if ($skippable) {
            $file = substr($file, 1);
        }

        if (!$this->filesystem->isAbsolutePath($file)) {
            $prefix = $package->isRoot()
                ? $package->getBaseDir()
                : $package->getVendorDir() . '/' . $package->getPrettyName();
            $file = $prefix . '/' . $file;
        }

        return $skippable . $this->filesystem->normalizePath($file);
    }
}

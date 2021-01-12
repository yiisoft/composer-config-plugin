<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Package;

use Composer\Package\PackageInterface;
use Yiisoft\Composer\Config\Package;

final class PackageFinder
{
    /**
     * Plain list of all project dependencies (including nested) as provided by composer.
     * The list is unordered (chaotic, can be different after every update).
     */
    private array $plainList = [];

    /**
     * Ordered list of package in form: package => depth.
     * For order description see {@see findPackages()}.
     */
    private array $orderedList = [];

    private PackageInterface $rootPackage;

    /**
     * @var PackageInterface[]
     */
    private array $packages;

    private string $vendorDir;

    public function __construct(string $vendorDir, PackageInterface $rootPackage, array $packages)
    {
        $this->rootPackage = $rootPackage;
        $this->packages = $packages;
        $this->vendorDir = $vendorDir;
    }

    /**
     * Returns ordered list of packages:

     * - Packages listed earlier in the composer.json will get earlier in the list.
     * - Children are listed before parents.
     *
     * @return Package[]
     */
    public function findPackages(): array
    {
        $root = new Package($this->rootPackage, $this->vendorDir);
        $this->plainList[$root->getPrettyName()] = $root;
        foreach ($this->packages as $package) {
            $this->plainList[$package->getPrettyName()] = new Package($package, $this->vendorDir);
        }
        $this->orderedList = [];
        $this->iteratePackage($root, true);

        $result = [];
        foreach (array_keys($this->orderedList) as $name) {
            /** @psalm-var array-key $name */
            $result[] = $this->plainList[$name];
        }

        return $result;
    }

    /**
     * Iterates through package dependencies.
     *
     * @param Package $package to iterate.
     * @param bool $includingDev process development dependencies, defaults to not process.
     */
    private function iteratePackage(Package $package, bool $includingDev = false): void
    {
        $name = $package->getPrettyName();

        // prevent infinite loop in case of circular dependencies
        static $processed = [];
        if (array_key_exists($name, $processed)) {
            return;
        }

        $processed[$name] = 1;

        // package depth in dependency hierarchy
        static $depth = 0;
        ++$depth;

        $this->iterateDependencies($package);
        if ($includingDev) {
            $this->iterateDependencies($package, true);
        }
        if (!array_key_exists($name, $this->orderedList)) {
            $this->orderedList[$name] = $depth;
        }

        --$depth;
    }

    /**
     * Iterates dependencies of the given package.
     *
     * @param Package $package
     * @param bool $dev type of dependencies to iterate: true - dev, default - general.
     */
    private function iterateDependencies(Package $package, bool $dev = false): void
    {
        $dependencies = $dev ? $package->getDevRequires() : $package->getRequires();
        foreach (array_keys($dependencies) as $target) {
            if (array_key_exists($target, $this->plainList) && empty($this->orderedList[$target])) {
                $this->iteratePackage($this->plainList[$target]);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Package;

use Composer\Composer;
use Yiisoft\Composer\Config\Package;

final class PackageFinder
{
    /**
     * Plain list of all project dependencies (including nested) as provided by composer.
     * The list is unordered (chaotic, can be different after every update).
     */
    private array $plainList = [];

    /**
     * Ordered list of package in form: package => depth
     * For order description @see findPackages.
     */
    private array $orderedList = [];

    private Composer $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * Returns ordered list of packages:
     * - listed earlier in the composer.json will get earlier in the list
     * - childs before parents.
     *
     * @return Package[]
     */
    public function findPackages(): array
    {
        $root = new Package($this->composer->getPackage(), $this->composer);
        $this->plainList[$root->getPrettyName()] = $root;
        foreach ($this->composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages() as $package) {
            $this->plainList[$package->getPrettyName()] = new Package($package, $this->composer);
        }
        $this->orderedList = [];
        $this->iteratePackage($root, true);

        $result = [];
        foreach (array_keys($this->orderedList) as $name) {
            $result[] = $this->plainList[$name];
        }

        return $result;
    }

    /**
     * Iterates through package dependencies.
     *
     * @param Package $package to iterate
     * @param bool $includingDev process development dependencies, defaults to not process
     */
    private function iteratePackage(Package $package, bool $includingDev = false): void
    {
        $name = $package->getPrettyName();

        // prevent infinite loop in case of circular dependencies
        static $processed = [];
        if (isset($processed[$name])) {
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
        if (!isset($this->orderedList[$name])) {
            $this->orderedList[$name] = $depth;
        }

        --$depth;
    }

    /**
     * Iterates dependencies of the given package.
     *
     * @param Package $package
     * @param bool $dev which dependencies to iterate: true - dev, default - general
     */
    private function iterateDependencies(Package $package, bool $dev = false): void
    {
        $deps = $dev ? $package->getDevRequires() : $package->getRequires();
        foreach (array_keys($deps) as $target) {
            if (isset($this->plainList[$target]) && empty($this->orderedList[$target])) {
                $this->iteratePackage($this->plainList[$target]);
            }
        }
    }
}

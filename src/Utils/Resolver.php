<?php

namespace Yiisoft\Composer\Config\Utils;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exceptions\CircularDependencyException;

/**
 * Resolver class.
 * Reorders files according to their cross dependencies
 * and resolves `$name` paths.
 */
class Resolver
{
    private array $dependenciesOrder = [];

    private array $dependencies = [];

    private array $following = [];

    private array $files;

    public function __construct(array $files)
    {
        $this->files = $files;

        $this->collectDeps($files);
        foreach (array_keys($files) as $name) {
            $this->followDeps($name);
        }
    }

    public function get(): array
    {
        $result = [];
        foreach ($this->dependenciesOrder as $name) {
            $result[$name] = $this->resolveDeps($this->files[$name]);
        }

        return $result;
    }

    private function resolveDeps(array $paths): array
    {
        foreach ($paths as &$path) {
            if ($this->isDependency($path)) {
                $dep = $this->parseDependencyName($path);

                $path = Builder::path($dep);
            }
        }

        return $paths;
    }

    private function followDeps(string $name): void
    {
        if (array_key_exists($name, $this->dependenciesOrder)) {
            return;
        }
        if (array_key_exists($name, $this->following)) {
            throw new CircularDependencyException($name . ' ' . implode(',', $this->following));
        }
        $this->following[$name] = $name;
        if (array_key_exists($name, $this->dependencies)) {
            foreach ($this->dependencies[$name] as $dep) {
                $this->followDeps($dep);
            }
        }
        $this->dependenciesOrder[$name] = $name;
        unset($this->following[$name]);
    }

    private function collectDeps(array $files): void
    {
        foreach ($files as $name => $paths) {
            foreach ($paths as $path) {
                if ($this->isDependency($path)) {
                    $dependencyName = $this->parseDependencyName($path);
                    if (!array_key_exists($name, $this->dependencies)) {
                        $this->dependencies[$name] = [];
                    }
                    $this->dependencies[$name][$dependencyName] = $dependencyName;
                }
            }
        }
    }

    private function isDependency(string $path): bool
    {
        return 0 === strncmp($path, '$', 1);
    }

    private function parseDependencyName(string $path): string
    {
        return substr($path, 1);
    }
}

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

        $this->collectDependencies($files);
        foreach (array_keys($files) as $name) {
            $this->followDependencies($name);
        }
    }

    public function get(): array
    {
        $result = [];
        foreach ($this->dependenciesOrder as $name) {
            $result[$name] = $this->resolveDependencies($this->files[$name]);
        }

        return $result;
    }

    private function resolveDependencies(array $paths): array
    {
        foreach ($paths as &$path) {
            if ($this->isDependency($path)) {
                $dependency = $this->parseDependencyName($path);

                $path = Builder::path($dependency);
            }
        }

        return $paths;
    }

    private function followDependencies(string $name): void
    {
        if (array_key_exists($name, $this->dependenciesOrder)) {
            return;
        }
        if (array_key_exists($name, $this->following)) {
            throw new CircularDependencyException($name . ' ' . implode(',', $this->following));
        }
        $this->following[$name] = $name;
        if (array_key_exists($name, $this->dependencies)) {
            foreach ($this->dependencies[$name] as $dependency) {
                $this->followDependencies($dependency);
            }
        }
        $this->dependenciesOrder[$name] = $name;
        unset($this->following[$name]);
    }

    private function collectDependencies(array $files): void
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

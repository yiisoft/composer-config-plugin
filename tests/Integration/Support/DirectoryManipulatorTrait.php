<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait DirectoryManipulatorTrait
{
    public function copyDirectory(string $source, string $destination): void
    {
        if (!file_exists($destination)) {
            mkdir($destination);
        }

        $directoryIterator = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $ignoredFolders = ['.idea', '.git', '.github', 'tests', 'vendor'];
        $filer = new \RecursiveCallbackFilterIterator(
            $directoryIterator,
            static function ($current, $key, \DirectoryIterator $iterator) use ($ignoredFolders) {
                return !in_array($iterator->getFilename(), $ignoredFolders);
            }
        );
        $iterator = new RecursiveIteratorIterator($filer, RecursiveIteratorIterator::SELF_FIRST);
        /* @var $item \SplFileInfo */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item->getPathname(), $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    public function removeDirectoryRecursive(string $path): void
    {
        $iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        /* @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isLink() || $file->isFile()) {
                unlink($file->getRealPath());
            } elseif ($file->isDir()) {
                rmdir($file->getRealPath());
            }
        }

        rmdir($path);
    }

    // public function copyFilesFromDirectory(string $source, string $destination): void
    // {
        
    //     $iterator = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
    //     $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
    //     print_r($iterator);
    //     // echo "\n";
    //     // print_r($destination);
    //     die;

    //     foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
    //         if ($item->isDir()) {
    //             mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
    //         } else {
    //             copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
    //         }
    //     }
    // }
}

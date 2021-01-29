<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\ConfigMerger\Modifier;

/**
 * Class ReverseValues
 *
 * This modifier reverses array values after merge.
 *
 * It should be specified as
 *
 * ```php
 * ReverseValues::class => new ReverseValues(),
 * ```
 *
 * Usage example:
 *
 * ```php
 *
 * use Yiisoft\Composer\Config\ConfigMerger\ReverseValues;
 *
 * $array1 = [
 *     'paths' => [
 *         '/tmp/tmp',
 *         ReverseValues::class => new ReverseValues(),
 *     ],
 * ];
 *
 * $array2 = [
 *     'paths' => [
 *         '/usr/bin',
 *     ],
 * ];
 *
 * $result = ConfigMerger::merge($array1, $array2);
 * ```
 *
 * The result will be
 *
 * ```php
 * [
 *     'paths' => [
 *         '/usr/bin',
 *         '/tmp/tmp',
 *     ],
 * ]
 * ```
 */
class ReverseValues implements ModifierInterface
{
    public function apply(array $data, $key): array
    {
        return array_reverse($data);
    }
}

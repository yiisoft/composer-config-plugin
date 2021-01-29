<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\ConfigMerger\Modifier;

/**
 * Removes array keys from the merge result while performing {@see ConfigMerger::merge()}.
 *
 * The modifier should be specified as
 *
 * ```php
 * RemoveKeys::class => new RemoveKeys(),
 * ```
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.0',
 * ];
 *
 * $b = [
 *    'version' => '1.1',
 *    'options' => [],
 *    RemoveKeys::class => new RemoveKeys(),
 * ];
 *
 * $result = ConfigMerger::merge($a, $b);
 * ```
 *
 * Will result in:
 *
 * ```php
 * [
 *     'Yii',
 *     '1.1',
 *     [],
 * ];
 */
final class RemoveKeys implements ModifierInterface
{
    public function apply(array $data, $key): array
    {
        return array_values($data);
    }
}

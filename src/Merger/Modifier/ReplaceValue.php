<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Merger\Modifier;

/**
 * Object that represents the replacement of array value while performing {@see Merger::merge()}.
 *
 * Usage example:
 *
 * ```php
 * $array1 = [
 *     'ids' => [
 *         1,
 *     ],
 *     'validDomains' => [
 *         'example.com',
 *         'www.example.com',
 *     ],
 * ];
 *
 * $array2 = [
 *     'ids' => [
 *         2,
 *     ],
 *     'validDomains' => new \Yiisoft\Composer\Config\Merger\Modifier\ReplaceValue([
 *         'yiiframework.com',
 *         'www.yiiframework.com',
 *     ]),
 * ];
 *
 * $result = Merger::merge($array1, $array2);
 * ```
 *
 * The result will be
 *
 * ```php
 * [
 *     'ids' => [
 *         1,
 *         2,
 *     ],
 *     'validDomains' => [
 *         'yiiframework.com',
 *         'www.yiiframework.com',
 *     ],
 * ]
 * ```
 */
final class ReplaceValue implements ModifierInterface
{
    /**
     * @var mixed value used as replacement
     */
    public $value;

    /**
     * @param mixed $value value used as replacement
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function apply(array $data, $key): array
    {
        /** @var mixed */
        $data[$key] = $this->value;

        return $data;
    }
}

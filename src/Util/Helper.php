<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Riimu\Kit\PHPEncoder\PHPEncoder;

/**
 * Helper class.
 */
class Helper
{
    /**
     * Returns PHP-executable string representation of given value.
     * Uses Riimu/Kit-PHPEncoder based `var_export` alternative.
     * And Opis/Closure to dump closures as PHP code.
     * @param mixed $value
     * @return string
     * @throws \ReflectionException
     */
    public static function exportVar($value): string
    {
        return static::getEncoder()->encode($value);
    }

    private static ?PHPEncoder $encoder = null;

    private static function getEncoder(): PHPEncoder
    {
        if (self::$encoder === null) {
            self::$encoder = static::createEncoder();
        }

        return self::$encoder;
    }

    private static function createEncoder(): PHPEncoder
    {
        \Opis\Closure\init();
        $encoder = new PHPEncoder([
            'object.format' => 'serialize',
        ]);
        $encoder->addEncoder(new ClosureEncoder(), true);
        $encoder->addEncoder(new EnvEncoder(), true);
        $encoder->addEncoder(new ObjectEncoder(), true);

        return $encoder;
    }
}

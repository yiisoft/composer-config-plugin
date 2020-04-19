<?php

namespace Yiisoft\Composer\Config\Util;

use Riimu\Kit\PHPEncoder\PHPEncoder;

/**
 * Helper class.
 */
class Helper
{
    public static function exportDefines(array $defines): string
    {
        $res = '';
        foreach ($defines as $key => $value) {
            $var = static::exportVar($value);
            $res .= "defined('$key') or define('$key', $var);\n";
        }

        return $res;
    }

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

    private static $encoder;

    private static function getEncoder()
    {
        if (self::$encoder === null) {
            self::$encoder = static::createEncoder();
        }

        return self::$encoder;
    }

    private static function createEncoder()
    {
        $encoder = new PHPEncoder([
            'object.format' => 'serialize',
        ]);
        $encoder->addEncoder(new ClosureEncoder(), true);

        return $encoder;
    }
}

<?php
namespace Yiisoft\Composer\Config\Utils;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Opis\Closure\ReflectionClosure;

/**
 * Closure encoder for Riimu Kit-PHPEncoder.
 */
class ClosureEncoder implements Encoder
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($value)
    {
        return $value instanceof Closure;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, $depth, array $options, callable $encode)
    {
        return (new ReflectionClosure($value))->getCode();
    }
}

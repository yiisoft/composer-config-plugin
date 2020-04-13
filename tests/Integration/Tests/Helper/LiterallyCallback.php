<?php
declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Helper;

use Closure;

final class LiterallyCallback
{
    /**
     * @var Closure
     */
    private Closure $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return Closure
     */
    public function getCallback(): Closure
    {
        return $this->callback;
    }
}

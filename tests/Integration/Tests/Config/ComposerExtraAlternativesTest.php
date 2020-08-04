<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class ComposerExtraAlternativesTest extends AlternativesTest
{
    protected function getEnvironmentName(): string
    {
        return 'composer-extra';
    }
}

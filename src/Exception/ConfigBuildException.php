<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Exception;

use Throwable;

final class ConfigBuildException extends Exception
{
    public function __construct(Throwable $previous)
    {
        $message = sprintf(
            <<<TEXT
                An error occurred during configuration build.
                The file being processed: "%s:%d".
                Error text:
                    %s
                Please, take care of fixing errors in this file before you continue.
                TEXT,
            $previous->getFile(),
            $previous->getLine(),
            $previous->getMessage()
        );

        parent::__construct($message, (int)$previous->getCode());
    }
}

<?php
declare(strict_types=1);

namespace Yiisoft\Composer\Config\Exception;

use Throwable;

final class ConfigBuildException extends Exception
{
    public function __construct(string $path, Throwable $previous)
    {
        $message = sprintf(
            <<<TEXT
                An error occured during configuration build.
                The file being processed: "%s".
                Error text:
                    %s
                Please, take care of fixing errors in this file before you continue.
                TEXT,
            $path,
            $previous->getMessage()
        );

        parent::__construct($message);
    }
}

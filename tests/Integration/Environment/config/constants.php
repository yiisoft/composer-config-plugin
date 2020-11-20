<?php

declare(strict_types=1);

defined('TEST_CONSTANT') || define('TEST_CONSTANT', 'a constant value defined in config/constants.php');
defined('ENV_STRING') || define('ENV_STRING', $_ENV['ENV_STRING'] ?? null);
defined('ENV_NUMBER') || define('ENV_NUMBER', $_ENV['ENV_NUMBER'] ?? null);
defined('ENV_TEXT') || define('ENV_TEXT', $_ENV['ENV_TEXT'] ?? null);

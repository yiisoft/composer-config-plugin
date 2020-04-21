<?php

defined('TEST_CONSTANT') or define('TEST_CONSTANT', 'a constant value defined in config/constants.php');
defined('ENV_STRING') or define('ENV_STRING', $_ENV['ENV_STRING'] ?? null);
defined('ENV_NUMBER') or define('ENV_NUMBER', $_ENV['ENV_NUMBER'] ?? null);
defined('ENV_TEXT') or define('ENV_TEXT', $_ENV['ENV_TEXT'] ?? null);

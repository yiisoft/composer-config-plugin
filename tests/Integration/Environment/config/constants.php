<?php

defined('TEST_CONSTANT') or define('TEST_CONSTANT', 'a constant value defined in config/constants.php');
defined('ENV_STRING') or define('ENV_STRING', getenv('ENV_STRING'));
defined('ENV_NUMBER') or define('ENV_NUMBER', getenv('ENV_NUMBER'));
defined('ENV_TEXT') or define('ENV_TEXT', getenv('ENV_TEXT'));

#!/usr/bin/env php
<?php

use Yiisoft\Composer\Config\Builder;

require_once dirname(__DIR__, 2) . '/autoload.php';

Builder::rebuild(__DIR__);

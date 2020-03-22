#!/usr/bin/env php
<?php

use Yiisoft\Composer\Config\Builder;

require_once dirname(dirname(__DIR__)) . '/autoload.php';

Builder::rebuild(__DIR__);

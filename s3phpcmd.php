#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

define('ROOT_PATH', __DIR__);

use Azadshaikh\S3phpCmd\Commands\Demo;
use Azadshaikh\S3phpCmd\Commands\Play;
use Azadshaikh\S3phpCmd\Commands\Listfiles;
use Symfony\Component\Console\Application;

$application = new Application();

// ... register commands
$application->add(new Listfiles());
$application->add(new Demo());
$application->run();
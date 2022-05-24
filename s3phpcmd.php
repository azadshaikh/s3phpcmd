#!/usr/bin/env php

<?php
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';
require ROOT_PATH . '/src/env.php';
require ROOT_PATH . '/src/flysystem.php';

// use Azadshaikh\S3phpCmd\Commands\Demo;
// use Azadshaikh\S3phpCmd\Commands\Play;
use Azadshaikh\S3phpCmd\Commands\Listfiles;
use Azadshaikh\S3phpCmd\Commands\VisibilityPublic;
use Azadshaikh\S3phpCmd\Commands\LocalBackup;
use Azadshaikh\S3phpCmd\Commands\SourceToDestination;
use Azadshaikh\S3phpCmd\Commands\LocalToS3;
use Azadshaikh\S3phpCmd\Commands\LocalToFtp;
use Symfony\Component\Console\Application;


$application = new Application();

// ... register commands
$application->add(new Listfiles());
$application->add(new VisibilityPublic());
$application->add(new LocalBackup());
$application->add(new SourceToDestination());
$application->add(new LocalToS3());
$application->add(new LocalToFtp());
// $application->add(new Demo());
$application->run();

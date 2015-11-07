<?php
require __DIR__.'/vendor/autoload.php';

use Bavarianlabs\App;
use Bavarianlabs\LoadData;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new App());
$application->add(new LoadData());
$application->run();
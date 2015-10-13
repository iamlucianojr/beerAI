<?php
/**
 * Created by PhpStorm.
 * User: luciano
 * Date: 13/10/15
 * Time: 19:38
 */

require __DIR__.'/vendor/autoload.php';

use Bavarianlabs\GreetCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new GreetCommand());
$application->run();
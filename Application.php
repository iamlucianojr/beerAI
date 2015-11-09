<?php
require __DIR__.'/vendor/autoload.php';

use Bavarianlabs\App;
use Bavarianlabs\Connection;
use Bavarianlabs\Beer\Harmonization;
use Bavarianlabs\LoadData;
use Bavarianlabs\Meat\Meat;
use Neoxygen\NeoClient\ClientBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(__DIR__.'/config/config.yml'))['db']['drive']['neo4j'];
$connection = new Connection(...array_values($config));

//#TODO Refactor
$clientDatabase = ClientBuilder::create()
    ->addConnection(
        'default',
        $connection->getSchema(),
        $connection->gethost(),
        $connection->getPort(),
        true,
        $connection->getUser(),
        $connection->getPassword()
    )
    ->setAutoFormatResponse(true)
    ->setDefaultTimeout(20)
    ->build()
;

$application = new Application();
$meat = new Meat();
$harmonization = new Harmonization();
# TODO: Use Symfony DI
$application->add(new App($clientDatabase, $meat, $harmonization));
$application->add(new LoadData());
$application->run();
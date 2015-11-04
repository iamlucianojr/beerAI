<?php

namespace Bavarianlabs;


use Neoxygen\NeoClient\ClientBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Yaml;

class LoadData extends Command
{
    /**
     * @var \Neoxygen\NeoClient\Client $clientDatabase
     */
    private $clientDatabase;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../config/config.yml'))['db']['drive']['neo4j'];
        $connection = new Connection(...array_values($config));

        //#TODO Refactor
        $this->clientDatabase = ClientBuilder::create()
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
    }

    protected function configure()
    {
        $this
            ->setName("beer:paladar:load")
            ->setDescription("Sistema especialista para harmonização de pratos e cervejas")
            ->setHelp("Criar base de dados");
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = fopen(__DIR__."/../resources/data_nodes.csv","r");

        while ($row = fgetcsv($file)) {
            $query = 'create (:'.$row[2].' {name: "'.$row[1].'"})';
            $this->clientDatabase->sendCypherQuery($query);
        }

        $output->writeln("Database gerada com sucesso");
    }
}
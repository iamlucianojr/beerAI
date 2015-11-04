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
        $this->purgeDatabase();

        $nodes = $this->getResourceFile();

        $this->inputData($nodes);

        $relationships = $this->getResourceFileRelationship();

        $this->inputRelationships($relationships);

        $output->writeln("Database gerada com sucesso");
    }

    /**
     * @return string
     */
    protected function purgeDatabase()
    {
        $query = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE n,r';
        $this->clientDatabase->sendCypherQuery($query);
    }

    /**
     * @return resource
     */
    protected function getResourceFile()
    {
        return fopen(__DIR__ . "/../resources/data_nodes.csv", "r");
    }

    /**
     * @param $file
     */
    protected function inputData($file)
    {
        while ($row = fgetcsv($file)) {
            $query = 'create (:' . $row[2] . ' {id: "' . $row[0] . '", name: "' . $row[1] . '"})';
            $this->clientDatabase->sendCypherQuery($query);
        }
    }

    private function inputRelationships($relationship)
    {
        while ($row = fgetcsv($relationship)) {
            $query = 'MATCH (a),(b) WHERE a.id = "' . $row[0] . '" AND b.id = "' . $row[1] . '" CREATE (a)-[r:' . $row[2] . ']->(b) RETURN r';
            $this->clientDatabase->sendCypherQuery($query);
        }
    }

    private function getResourceFileRelationship()
    {
        return fopen(__DIR__ . "/../resources/data_relationships.csv", "r");
    }
}
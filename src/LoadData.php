<?php

namespace Bavarianlabs;


use Neoxygen\NeoClient\ClientBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class LoadData extends Command
{
    /**
     * @var \Neoxygen\NeoClient\Client $clientDatabase
     */
    private $clientDatabase;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/../config/sample.config.yml'))['db']['drive']['neo4j'];
        $connection = new Connection(...array_values($config));

        //#TODO Refactor
        $this->clientDatabase = ClientBuilder::create()
            ->addConnection(
                'default',
                $connection->getSchema(),
                $connection->gethost(),
                (int) $connection->getPort(),
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
        $tx = $this->clientDatabase->prepareTransaction();
        while ($row = fgetcsv($file)) {
            $query = 'create (:' . $row[2] . ' {id: {id}, name: {name}})';
            $tx->pushQuery($query, ['id' => $row[0], 'name' => $row['1']]);
        }
        $tx->commit();
    }

    private function inputRelationships($relationship)
    {
        $tx = $this->clientDatabase->prepareTransaction();
        while ($row = fgetcsv($relationship)) {
            $query = 'MATCH (a),(b) WHERE a.id = {aid} AND b.id = {bid} CREATE (a)-[r:' . $row[2] . ']->(b) RETURN r';
            $p = ['aid' => $row[0], 'bid' => $row[1]];
            $tx->pushQuery($query, $p);
        }
        $tx->commit();
    }

    private function getResourceFileRelationship()
    {
        return fopen(__DIR__ . "/../resources/data_relationships.csv", "r");
    }
}
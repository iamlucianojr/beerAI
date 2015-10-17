<?php

namespace Bavarianlabs;


use Neoxygen\NeoClient\ClientBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Yaml;

class App extends Command
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
            ->setName("beer:paladar")
            ->setDescription("Sistema especialista para harmonização de pratos e cervejas")
            ->setHelp("Responda as perguntas para que possamos recomendar a melhor combinação");
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = ['limit' => 50];
        $query = 'MATCH (ee:BeerNodes) WHERE ee.type = "BeerType" and ee.name = "stout" RETURN ee LIMIT {limit}';

        $dataResult = $this->clientDatabase->sendCypherQuery($query, $params)->getResult();

        var_dump($dataResult); exit;

        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select your favorite colors (defaults to red and blue)',
            array('red', 'blue', 'yellow'),
            '0,1'
        );
        $question->setMultiselect(true);

        $colors = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected: ' . implode(', ', $colors));

        $this->execute($input, $output);
    }
}
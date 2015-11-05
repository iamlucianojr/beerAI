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
        $this->questionAboutFood($input, $output);
    }

    private function questionAboutFood(InputInterface $input, OutputInterface $output)
    {
        $foodOptions = $this->getFoodOptions();

        $question = new ChoiceQuestion(
            'Por favor informe o tipo da sua refeição',
            $foodOptions
        );

        $helper = $this->getHelper('question');

        $food = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $food);

        $this->execute($input, $output);
    }

    /**
     * @return \Neoxygen\NeoClient\Formatter\Result[]
     */
    private function getFoodOptions()
    {
        $query = 'MATCH (n:Comida) RETURN DISTINCT n';

        $foodOptions = $this->clientDatabase->sendCypherQuery($query)->getResult();

        $arrOptions = array();
        foreach ($foodOptions->getNodes() as $node) {
            $arrOptions[] = $node->getProperty('name');
        }
        return $arrOptions;
    }
}
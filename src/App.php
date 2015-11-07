<?php

namespace Bavarianlabs;


use Bavarianlabs\Meat\Meat;
use Bavarianlabs\Meat\MeatInterface;
use Bavarianlabs\Question\ChoiceQuestion;
use Neoxygen\NeoClient\ClientBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class App extends Command
{
    private $answers = array();
    /**
     * @var MeatInterface
     */
    private $meat;
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

        $this->meat = new Meat();
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
        $this->questionAboutMeat($input, $output);
        $this->questionAboutHarmonization($input, $output);
        $this->execute($input, $output);
    }

    private function questionAboutMeat(InputInterface $input, OutputInterface $output)
    {
        $meatOptions = $this->meat->getMeatOptions($this->clientDatabase);

        $question = new ChoiceQuestion(
            'Por favor informe o tipo da sua refeição',
            $meatOptions
        );

        $helper = $this->getHelper('question');

        $meat = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $meat);

        $this->answers['meat'] = $meat;
    }

    private function questionAboutHarmonization($input, $output)
    {
    }
}
<?php

namespace Bavarianlabs;


use Bavarianlabs\Beer\HarmonizationInterface;
use Bavarianlabs\Meat\MeatInterface;
use Bavarianlabs\Question\ChoiceQuestion;
use Neoxygen\NeoClient\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * @var HarmonizationInterface
     */
    private $harmonization;

    /**
     * App constructor.
     * @param Client $connection
     * @param MeatInterface $meat
     * @param HarmonizationInterface $harmonization
     */
    public function __construct(Client $connection, MeatInterface $meat, HarmonizationInterface $harmonization)
    {
        parent::__construct();
        $this->meat = $meat;
        $this->harmonization = $harmonization;
        $this->clientDatabase = $connection;
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
//        $this->execute($input, $output);
    }

    private function questionAboutMeat(InputInterface $input, OutputInterface $output)
    {
        $meatOptions = $this->meat->getMeatOptions($this->clientDatabase);

        $text = 'Por favor informe o tipo da sua refeição';

        $question = $this->buildQuestion($meatOptions, $text);

        $helper = $this->getHelper('question');

        $meat = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: '.$meat);

        $this->answers['meat'] = $meat;
    }

    private function questionAboutHarmonization(InputInterface $input, OutputInterface $output)
    {
        $harmonizationOptions = $this->harmonization->getHarmonizationOptions($this->clientDatabase);

        $text = 'Por favor informe o tipo de harmonização desejado';

        $question = $this->buildQuestion($harmonizationOptions, $text);

        $helper = $this->getHelper('question');

        $answer = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: '.$answer);

        $this->answers['harmonization'] = $answer;
    }

    /**
     * @param $meatOptions
     * @param $text
     * @return ChoiceQuestion
     */
    private function buildQuestion($meatOptions, $text)
    {
        $question = new ChoiceQuestion(
            $text,
            $meatOptions
        );
        return $question;
    }
}
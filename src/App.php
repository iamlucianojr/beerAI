<?php

namespace Bavarianlabs;


use Bavarianlabs\Beer\AlcoholLevel;
use Bavarianlabs\Beer\Attribute\Color;
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
     * @var AlcoholLevel
     */
    private $alcoholLevel;

    /**
     * @var Color
     */
    private $color;

    /**
     * App constructor.
     * @param Client $connection
     * @param MeatInterface $meat
     * @param HarmonizationInterface $harmonization
     * @param AlcoholLevel $alcoholLevel
     * @param Color $color
     */
    public function __construct(Client $connection, MeatInterface $meat, HarmonizationInterface $harmonization, AlcoholLevel $alcoholLevel, Color $color)
    {
        parent::__construct();
        $this->meat             = $meat;
        $this->harmonization    = $harmonization;
        $this->alcoholLevel     = $alcoholLevel;
        $this->clientDatabase   = $connection;
        $this->color            = $color;
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
        $this->questionAboutAlcoholLevel($input, $output);
        $this->questionAboutColor($input, $output);
        $this->recommendBeers($input, $output);
    }

    private function questionAboutMeat(InputInterface $input, OutputInterface $output)
    {
        $meatOptions = $this->meat->getMeatOptions($this->clientDatabase);

        $text = 'Por favor informe o tipo da sua refeição';

        $question = $this->buildQuestion($meatOptions, $text);

        $helper = $this->getHelper('question');

        $meat = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $meat);

        $this->answers[0] = array(
            'label'         => 'Comida',
            'choice'        => $meat
        );
    }

    private function questionAboutHarmonization(InputInterface $input, OutputInterface $output)
    {
        $harmonizationOptions = $this->harmonization->getHarmonizationOptions($this->clientDatabase);

        $text = 'Por favor informe o tipo de harmonização desejado';

        $question = $this->buildQuestion($harmonizationOptions, $text);

        $helper = $this->getHelper('question');

        $answer = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $answer);

        $this->answers[0]['relationship'] = $this->harmonization->getOption($answer);
    }

    private function questionAboutAlcoholLevel(InputInterface $input, OutputInterface $output)
    {
        $alcoholLevelOptions = $this->alcoholLevel->getAlcoholOptions($this->clientDatabase);

        $text = 'Qual o teor alcólico você prefere';

        $question = $this->buildQuestion($alcoholLevelOptions, $text);

        $helper = $this->getHelper('question');

        $answer = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $answer);

        $this->answers[] = array(
            'label'         => 'NivelAlcolico',
            'relationship'  => 'TEM_PERCENTUAL_ALCOLICO',
            'choice'        => $answer
        );
    }

    private function questionAboutColor(InputInterface $input, OutputInterface $output)
    {
        $colorOptions = $this->color->getColorOptions($this->clientDatabase);

        $text = 'Qual a coloração de cerveja preferida';

        $question = $this->buildQuestion($colorOptions, $text);

        $helper = $this->getHelper('question');

        $answer = $helper ->ask($input, $output, $question);

        $output->writeln('Você selecionou: ' . $answer);

        $this->answers[] = array(
            'label'         => 'BeerColor',
            'relationship'  => 'TEM_COLORACAO',
                'choice'        => $answer
        );
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

    private function recommendBeers(InputInterface $input, OutputInterface $output)
    {
        $query = 'MATCH ';
        foreach ($this->answers as $item) {
            $query .= ' (:' . $item['label'] . '{ name: "' . $item['choice'] . '" })<-[:' . $item['relationship'] . ']-(beer:BeerBrand),';
        }

        $query = substr($query, 0, -1);

        $query.= " return beer ";

        $result = $this->clientDatabase->sendCypherQuery($query)->getResult();

        $arrResult = array();
        foreach ($result->getNodes() as $node) {
            $arrResult[] = $node->getProperty('name');
        }

        $output->writeln('As opções de cervejas possíveis são(é): ' . implode(',', $arrResult));

    }
}
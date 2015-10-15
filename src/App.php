<?php

namespace Bavarianlabs;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class App extends Command
{
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
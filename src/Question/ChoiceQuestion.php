<?php

namespace Bavarianlabs\Question;


use Symfony\Component\Console\Question\ChoiceQuestion as BaseChoiceQuestion;

class ChoiceQuestion extends BaseChoiceQuestion
{
    public function __construct($question, array $choices, $default = null)
    {
        parent::__construct($question, $choices, $default);
        parent::setErrorMessage("Infelizmente não possuiamos essa opção, caso você ache relevante incluir em nossa database a opção desejada contribua com o projeto em: https://github.com/luciano-jr/beerAI/");
    }




}
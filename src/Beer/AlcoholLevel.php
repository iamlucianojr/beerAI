<?php

namespace Bavarianlabs\Beer;

use Neoxygen\NeoClient\Client;

class AlcoholLevel
{
    const FORTE = 'Teor Forte';
    const MEDIO = 'Teor Médio';
    const BAIXO = 'Teor Baixo';

    function getAlcoholOptions(Client $clientDatabase)
    {
        return array(
            $this::FORTE,
            $this::MEDIO,
            $this::BAIXO
        );
    }
}
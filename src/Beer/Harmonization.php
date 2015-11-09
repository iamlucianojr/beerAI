<?php

namespace Bavarianlabs\Beer;


use Neoxygen\NeoClient\Client;

class Harmonization implements HarmonizationInterface
{
    const E_SEMELHANTE = 'E_SEMELHANTE';
    const E_CONSTRASTE = 'E_CONSTRASTE';
    const E_EQUILIBRIO = 'E_EQUILIBRIO';

    function getHarmonizationOptions(Client $clientDatabase)
    {
        #TODO Refactor it to get a real data
        return array(
            $this::E_CONSTRASTE => "Contraste",
            $this::E_EQUILIBRIO => 'Equilibrado',
            $this::E_SEMELHANTE => 'Semelhança'
        );
    }
}
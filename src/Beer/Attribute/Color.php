<?php

namespace Bavarianlabs\Beer\Attribute;


use Neoxygen\NeoClient\Client;

class Color
{

    public function getColorOptions(Client $clientDatabase)
    {
        $query = 'MATCH (n:BeerColor) RETURN DISTINCT n';

        $foodOptions = $clientDatabase->sendCypherQuery($query)->getResult();

        $arrOptions = array();
        foreach ($foodOptions->getNodes() as $node) {
            $arrOptions[] = $node->getProperty('name');
        }
        return $arrOptions;
    }
}
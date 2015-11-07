<?php

namespace Bavarianlabs\Meat;


use Neoxygen\NeoClient\Client;

class Meat implements MeatInterface
{

    function getMeatOptions(Client $clientDatabase)
    {
        $query = 'MATCH (n:Comida) RETURN DISTINCT n';

        $foodOptions = $clientDatabase->sendCypherQuery($query)->getResult();

        $arrOptions = array();
        foreach ($foodOptions->getNodes() as $node) {
            $arrOptions[] = $node->getProperty('name');
        }
        return $arrOptions;
    }
}
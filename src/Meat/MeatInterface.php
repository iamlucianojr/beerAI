<?php

namespace Bavarianlabs\Meat;


use Neoxygen\NeoClient\Client;

interface MeatInterface
{
    function getMeatOptions(Client $clientDatabase);
}
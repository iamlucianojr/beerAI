<?php

namespace Bavarianlabs\Beer;


use Neoxygen\NeoClient\Client;

interface HarmonizationInterface
{
    function getHarmonizationOptions(Client $clientDatabase);
}
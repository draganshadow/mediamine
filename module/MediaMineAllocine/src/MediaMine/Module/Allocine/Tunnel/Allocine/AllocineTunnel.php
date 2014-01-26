<?php
namespace MediaMine\Module\Allocine\Tunnel\Allocine;

use MediaMine\Tunnel\AbstractTunnel;
use MediaMine\Tunnel\Allocine\Parser\PersonParser;

class AllocineTunnel extends AbstractTunnel
{
    public function getAbilities() {
        return array(
            'Person' => array()
        );
    }

    public function searchPerson($name) {
        $personParser = new PersonParser();
        return $personParser->parse($name);
    }
}

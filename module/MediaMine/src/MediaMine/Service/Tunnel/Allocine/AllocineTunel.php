<?php
namespace MediaMine\Service\Tunnel\Allocine;

use MediaMine\Service\Tunnel\AbstractTunnel;
use MediaMine\Service\Tunnel\Allocine\Parser\PersonParser;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class AllocineTunnel extends AbstractTunnel implements ServiceLocatorAwareInterface
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

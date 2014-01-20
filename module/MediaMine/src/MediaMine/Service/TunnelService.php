<?php
namespace MediaMine\Service;

use MediaMine\Service\Tunnel\Allocine\AllocineTunnel;
use MediaMine\Service\Tunnel\XML\XMLTunnel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class TunnelService extends AbstractService implements ServiceLocatorAwareInterface
{

    /**
     * @var \MediaMine\Service\Tunnel\XML\XMLTunnel
     */
    protected $xmlTunnel;

    /**
     * @var \MediaMine\Service\Tunnel\Allocine\AllocineTunnel
     */
    protected $allocineTunnel;

    /**
     * @return \MediaMine\Service\Tunnel\Allocine\AllocineTunnel
     */
    public function getAllocineTunnel()
    {
        if (null === $this->allocineTunnel) {
            $this->allocineTunnel = new AllocineTunnel();
            $this->allocineTunnel->setServiceLocator($this->getServiceLocator());
        }
        return $this->allocineTunnel;
    }

    /**
     * @return \MediaMine\Service\Tunnel\XML\XMLTunnel
     */
    public function getXmlTunnel()
    {
        if (null === $this->xmlTunnel) {
            $this->xmlTunnel = new XMLTunnel();
            $this->xmlTunnel->setServiceLocator($this->getServiceLocator());
        }
        return $this->xmlTunnel;
    }


}

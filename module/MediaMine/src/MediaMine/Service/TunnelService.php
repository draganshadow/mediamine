<?php
namespace MediaMine\Service;

use MediaMine\Tunnel\XML\XMLTunnel;
use MediaMine\Module\Allocine\Tunnel\Allocine\AllocineTunnel;

class TunnelService extends AbstractService
{
    /**
     * @var \MediaMine\Service\Tunnel\XML\XMLTunnel
     */
    protected $xmlTunnel;

    /**
     * @var \MediaMineAllocine\Tunnel\Allocine\AllocineTunnel
     */
    protected $allocineTunnel;

    /**
     * @return \MediaMineAllocine\Tunnel\Allocine\AllocineTunnel
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
     * @return \MediaMine\Tunnel\XML\XMLTunnel
     */
    public function getXmlTunnel()
    {
        if (null === $this->xmlTunnel) {
            $this->xmlTunnel = $this->getServiceLocator()->get('XML');
//            $this->xmlTunnel->setLogger($this->getServiceLocator()->get('mediamine-tunnel-log'));
//            $this->xmlTunnel->setServiceLocator($this->getServiceLocator());
        }
        return $this->xmlTunnel;
    }


}

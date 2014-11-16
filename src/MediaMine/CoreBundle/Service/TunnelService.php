<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Tunnel;
use MediaMine\CoreBundle\Shared\BatchAware;
use MediaMine\CoreBundle\Shared\ContainerAware;

/**
 * @Service("mediamine.service.tunnel")
 */
class TunnelService extends AbstractService
{
    use BatchAware;
    use ContainerAware;

    public function getTunnelEntity($key)
    {
        $tunnelRepository = $this->getRepository('System\Tunnel');
        $queryParam = array(
            'key' => $key
        );
        $tunnels = $tunnelRepository->findFullBy($queryParam);
        if (!count($tunnels)) {
            return false;
        }
        return $tunnels[0];
    }

    public function enableTunnel($key)
    {
        $tunnelRepository = $this->getRepository('System\Tunnel');
        $queryParam = array(
            'key' => $key
        );
        $tunnels = $tunnelRepository->findFullBy($queryParam);
        if (!count($tunnels)) {
            return array('error' => 1);
        }
        /**
         * @var $tunnel Tunnel
         */
        $tunnel = $tunnels[0];
        $tunnel->setEnabled(true);

        $tunnelService = $this->getContainer()->get($tunnel->getService());
        $tunnelService->enableTunnel();

        $this->getEntityManager()->persist($tunnel);
        $this->batch(1);

        return $tunnel->getArrayCopy();
    }

    public function disableTunnel($key)
    {
        $tunnelRepository = $this->getRepository('System\Tunnel');
        $queryParam = array(
            'key' => $key
        );
        $tunnels = $tunnelRepository->findFullBy($queryParam);
        if (!count($tunnels)) {
            return array('error' => 1);
        }
        /**
         * @var $tunnel Tunnel
         */
        $tunnel = $tunnels[0];
        $tunnel->setEnabled(false);

        $tunnelService = $this->getContainer()->get($tunnel->getService());
        $tunnelService->disableTunnel();

        $this->getEntityManager()->persist($tunnel);
        $this->batch(1);
        return $tunnel->getArrayCopy();
    }
}

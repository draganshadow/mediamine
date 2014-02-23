<?php
namespace MediaMine\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;

class TunnelAbstractFactory extends \Netsyos\Common\Factory\ServiceAbstractFactory
{
    /**
     * @return string
     */
    public function getBaseNamespace()
    {
        return 'MediaMine\Tunnel\\';
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        if (class_exists($this->getBaseNamespace() . str_replace('Tunnel', '', $requestedName) . '\\' . $requestedName)){
            return true;
        }
        return false;
    }

    public function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        $class = $this->getBaseNamespace() . str_replace( 'Tunnel', '', $requestedName) . '\\' . $requestedName;
        $service = new $class;
        $service->setLogger($locator->get('mediamine-tunnel-log'));
        return $service;
    }
}
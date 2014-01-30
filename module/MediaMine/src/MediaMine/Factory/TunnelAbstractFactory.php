<?php
namespace MediaMine\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TunnelAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        if (class_exists('\MediaMine\Tunnel\\' . str_replace('Tunnel', '', $requestedName) . '\\' . $requestedName)){
            return true;
        }
        return false;
    }

    public function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        $class = '\MediaMine\Tunnel\\' . str_replace( 'Tunnel', '', $requestedName) . '\\' . $requestedName;
        $service = new $class;
        $service->setLogger($locator->get('mediamine-tunnel-log'));
        return $service;
    }
}
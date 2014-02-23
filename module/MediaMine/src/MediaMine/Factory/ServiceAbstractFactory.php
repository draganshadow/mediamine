<?php
namespace MediaMine\Factory;

class ServiceAbstractFactory extends \Netsyos\Common\Factory\ServiceAbstractFactory
{
    /**
     * @return string
     */
    public function getBaseNamespace()
    {
        return 'MediaMine\Service\\';
    }
}
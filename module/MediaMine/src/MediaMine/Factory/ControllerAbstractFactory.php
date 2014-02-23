<?php
namespace MediaMine\Factory;


class ControllerAbstractFactory extends \Netsyos\Common\Factory\ControllerAbstractFactory
{
    /**
     * @return string
     */
    public function getBaseNamespace()
    {
        return 'MediaMine\Controller\\';
    }
}
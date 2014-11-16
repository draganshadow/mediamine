<?php
namespace MediaMine\CoreBundle\Shared;

use Symfony\Component\DependencyInjection\Container;

trait ContainerAware {

    /**
     * @Inject("service_container")
     * @var Container
     */
    public $container;

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
} 
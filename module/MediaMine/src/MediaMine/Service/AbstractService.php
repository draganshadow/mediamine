<?php
namespace MediaMine\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

class AbstractService implements ServiceLocatorAwareInterface
{
    protected $insert = 0;
    protected $batchSize = 20000;

    protected $baseNameSpace = 'MediaMine\Entity\\';
    /**
     * Service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Set the service locator.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    protected function getRepository($entity) {
        return $this->getEntityManager()->getRepository($this->baseNameSpace . $entity);
    }

    /**
     * @param bool $force
     */
    protected function flush($force = false) {
        $this->insert++;
        if ($this->insert % $this->batchSize == 0 || $force) {
            $this->getEntityManager()->flush();
        }
    }
}

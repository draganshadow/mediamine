<?php
namespace MediaMine\Service;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use MediaMine\Initializer\LoggerAwareInterface;
use MediaMine\Repository\EntityRepository;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

class AbstractService implements ServiceLocatorAwareInterface, ObjectManagerAwareInterface, LoggerAwareInterface
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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @param string $entity
     * @return EntityRepository
     */
    protected function getRepository($entity) {
        return $this->getEntityManager()->getRepository($this->baseNameSpace . $entity);
    }

    /**
     * @param bool $force
     */
    protected function flush($force = false) {
        //TODO move that function to a better place

        $this->insert++;
        if ($this->insert % $this->batchSize == 0 || $force) {
            $this->getEntityManager()->flush();
        }
    }

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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getObjectManager();
    }

    /**
     * Set the object manager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function setObjectManager(\Doctrine\Common\Persistence\ObjectManager $objectManager) {
        $this->entityManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function getObjectManager()
    {
        return $this->entityManager;
    }

    /**
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(\Zend\Log\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}

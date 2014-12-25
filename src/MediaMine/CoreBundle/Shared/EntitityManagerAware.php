<?php
namespace MediaMine\CoreBundle\Shared;
use MediaMine\CoreBundle\Repository\AbstractRepository;

trait EntitityManagerAware {

    /**
     * @var string
     */
    protected $baseNameSpace = 'MediaMine\\CoreBundle\\Entity\\';

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     * @Inject("doctrine")
     */
    public $entityManagerRegistry;

    /**
     * @param $entity
     * @param bool $fullName
     * @return AbstractRepository
     */
    public function getRepository($entity, $fullName = false)
    {
        return $this->getEntityManager()->getRepository(($fullName ? '' : $this->getBaseNameSpace()) . $entity);
    }

    /**
     * @return string
     */
    public function getBaseNameSpace()
    {
        return $this->baseNameSpace;
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManagerRegistry->getManager();
    }

    /**
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    public function getEntityManagerRegistry()
    {
        return $this->entityManagerRegistry;
    }

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $entityManagerRegistry
     */
    public function setEntityManagerRegistry($entityManagerRegistry)
    {
        $this->entityManagerRegistry = $entityManagerRegistry;
    }

}
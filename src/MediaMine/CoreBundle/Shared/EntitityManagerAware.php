<?php
namespace MediaMine\CoreBundle\Shared;
use MediaMine\CoreBundle\Repository\AbstractRepository;

trait EntitityManagerAware {

    /**
     * @var string
     */
    protected $baseNameSpace = 'MediaMine\\CoreBundle\\Entity\\';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     * @Inject("doctrine.orm.entity_manager")
     */
    public $entityManager;

    /**
     * @param $entity
     * @param bool $fullName
     * @return AbstractRepository
     */
    public function getRepository($entity, $fullName = false)
    {
        return $this->entityManager->getRepository(($fullName ? '' : $this->getBaseNameSpace()) . $entity);
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
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
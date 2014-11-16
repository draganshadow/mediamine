<?php
namespace MediaMine\CoreBundle\Shared;

trait MongoEntitityManagerAware
{

    /**
     * @var \Doctrine\Bundle\MongoDBBundle\ManagerRegistry
     * @Inject("doctrine_mongodb")
     */
    public $doctrineMongoRegistry;

    /**
     * @var string
     */
    protected $documentNameSpace = 'MediaMine\\CoreBundle\\Document\\';

    /**
     * @return string
     */
    public function getDocumentNameSpace()
    {
        return $this->documentNameSpace;
    }

    /**
     * @return \Doctrine\Bundle\MongoDBBundle\ManagerRegistry
     */
    public function getDoctrineMongoRegistry()
    {
        return $this->doctrineMongoRegistry;
    }

    /**
     * @param \Doctrine\Bundle\MongoDBBundle\ManagerRegistry $doctrineMongoRegistry
     */
    public function setDoctrineMongoRegistry($doctrineMongoRegistry)
    {
        $this->doctrineMongoRegistry = $doctrineMongoRegistry;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getMongoManager()
    {
        return $this->doctrineMongoRegistry->getManager();
    }

    /**
     * @param $entity
     * @param bool $fullName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getMongoRepository($entity, $fullName = false)
    {
        return $this->doctrineMongoRegistry->getManager()->getRepository(($fullName ? '' : $this->getDocumentNameSpace()) . $entity);
    }
}
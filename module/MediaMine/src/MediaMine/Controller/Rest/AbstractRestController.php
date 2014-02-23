<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use Netsyos\Common\Initializer\EntityManagerAwareInterface;
use Zend\Mvc\Controller\AbstractRestfulController;

class AbstractRestController extends AbstractRestfulController implements EntityManagerAwareInterface
{
    /**
     * @var string
     */
    protected $baseNameSpace = '';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @param string $baseNameSpace
     * @return mixed|void
     */
    public function setBaseNameSpace($baseNameSpace = false)
    {
        if (!$baseNameSpace) {
            $class = explode('\\', get_class($this));
            unset($class[count($class) - 1]);
            unset($class[count($class) - 1]);
            $this->baseNameSpace = implode('\\', $class) . '\Entity\\';
        } else {
            $this->baseNameSpace = $baseNameSpace;
        }
    }

    /**
     * @return string
     */
    public function getBaseNameSpace()
    {
        return $this->baseNameSpace;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return mixed
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param $entity
     * @param bool $namespace
     * @return \Netsyos\Common\Repository\EntityRepository
     */
    public function getRepository($entity, $namespace = false)
    {
        return $this->entityManager->getRepository(($namespace ? $namespace : $this->baseNameSpace) . $entity);
    }
}
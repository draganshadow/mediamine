<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;

class AbstractRestController extends AbstractRestfulController implements EntityManagerAware
{
    private $em;
    private $es;
    protected $baseNameSpace = 'MediaMine\Entity\\';

    public function getEm()
    {
        return $this->em;
    }

    public function setEm($em)
    {
        $this->em = $em;
    }

    public function setEs($es)
    {
        $this->es = $es;
    }

    public function getEs()
    {
        return $this->es;
    }

    protected function getRepository($entity) {
        return $this->getEm()->getRepository($this->baseNameSpace . $entity);
    }
}
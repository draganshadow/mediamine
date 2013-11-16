<?php
namespace MediaMine\Controller;

use Doctrine\ORM\Query;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController implements EntityManagerAware, ElasticsearchAware
{

    protected $baseNameSpace = 'MediaMine\Entity\\';

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var
     */
    private $es;

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
        return $this->em->getRepository($this->baseNameSpace . $entity);
    }
}
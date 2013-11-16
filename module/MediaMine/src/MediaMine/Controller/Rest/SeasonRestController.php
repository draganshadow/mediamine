<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SeasonRestController extends AbstractRestController implements EntityManagerAware
{
    public function getList()
    {
    }

    public function get($id)
    {
        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');

        $season = $this->getEm()->find('MediaMine\Entity\Video\Season', $id);

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Video', 'i')
            ->from('MediaMine\Entity\Video\Video','Video')
            ->innerJoin('Video.season', 'season', 'WITH', 'season.id = :id')
            ->join('Video.images', 'i')
            ->setParameter('id', $id)
            ->orderBy('Video.name', 'ASC');
        $resultSet = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel(array(
            'data' => $season->getArrayCopy(),
            'episodes' => $resultSet
        ));
    }

    public function create($data)
    {
        # code...
    }

    public function update($id, $data)
    {
        # code...
    }

    public function delete($id)
    {
        # code...
    }
}
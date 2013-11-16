<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/season",
 *      basePath="/api"
 * )
 */
class SeasonRestController extends AbstractRestController implements EntityManagerAware
{
    public function getList()
    {
    }

    /**
     *  @SWG\Api(
     *      path="/season/{id}",
     *      @SWG\Operation(
     *          nickname="getSeason",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="id",
     *                  description="ID of pet that needs to be fetched",
     *                  paramType="path",
     *                  required="true",
     *                  type="scalar"
     *              )
     *          )
     *      )
     *  )
     */
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
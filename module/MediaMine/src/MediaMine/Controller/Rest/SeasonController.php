<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/season",
 *      basePath="/api"
 * )
 */
class SeasonController extends AbstractRestController
{
    /**
     *  @SWG\Api(
     *      path="/season",
     *      @SWG\Operation(
     *          nickname="listSeason",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="serie",
     *                  description="The Serie ID",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              )
     *          )
     *      )
     *  )
     */
    public function getList()
    {
        $params = array();
        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Season', 'g', 'i')
            ->from('MediaMine\Entity\Video\Season','Season');

        $serie = (int) $this->params()->fromQuery('serie', null);
        if ($serie != null) {
            $qb->innerJoin('Season.group', 'g', 'WITH', 'g.id = :serie');
            $params['serie'] = $serie;
        } else {
            $qb->innerJoin('Season.group', 'g');
        }
        $qb->join('Season.images', 'i');

        $o = 'ASC';
        $order = $this->params()->fromQuery('order', null);
        if ($order == 'DESC') {
            $o = 'DESC';
        }
        $by = 'number';
        $orderBy = $this->params()->fromQuery('orderBy', null);
        if ($orderBy != null) {
            $by = $orderBy;
        }

        $qb->orderBy('Season.' . $by, $o);
        $resultSet = $qb->setParameters($params)->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return new JsonModel($resultSet);
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

        return new JsonModel($season->getArrayCopy());
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
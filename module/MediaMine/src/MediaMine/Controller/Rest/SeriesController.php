<?php
namespace MediaMine\Controller\Rest;

use Swagger\Annotations as SWG;
use Doctrine\ORM\Query;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/series",
 *      basePath="/api"
 * )
 */
class SeriesController extends AbstractRestController
{
    /**
     *  @SWG\Api(
     *      path="/series",
     *      @SWG\Operation(
     *          nickname="listSeries",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="order",
     *                  description="The Order",
     *                  paramType="query",
     *                  required="false",
     *                  type="string",
     *                  enum="['ASC','DESC']"
     *              )
     *          )
     *      )
     *  )
     */
    public function getList()
    {
        $order = $this->params()->fromQuery('order', 'ASC');

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('g, i')
            ->from('MediaMine\Entity\Video\Group','g')
            ->join('g.images', 'i')
            ->orderBy('g.name', $order);
        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel($results);
    }

    /**
     *  @SWG\Api(
     *      path="/series/{id}",
     *      @SWG\Operation(
     *          nickname="getSeries",
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
        $serie = $this->getEntityManager()->find('MediaMine\Entity\Video\Group', $id);
        return new JsonModel($serie->getArrayCopy());
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
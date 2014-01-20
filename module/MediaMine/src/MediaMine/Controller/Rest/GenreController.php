<?php
namespace MediaMine\Controller\Rest;

use Swagger\Annotations as SWG;
use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/genre",
 *      basePath="/api"
 * )
 */
class GenreController extends AbstractRestController implements EntityManagerAware
{
    /**
     *  @SWG\Api(
     *      path="/genre",
     *      @SWG\Operation(
     *          nickname="listGenres",
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

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('g')
            ->from('MediaMine\Entity\Video\Genre','g')
            ->orderBy('g.name', $order);
        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel($results);
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
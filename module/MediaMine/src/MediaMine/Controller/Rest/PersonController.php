<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/person",
 *      basePath="/api"
 * )
 */
class PersonController extends AbstractRestController implements EntityManagerAware
{
    /**
     *  @SWG\Api(
     *      path="/person",
     *      @SWG\Operation(
     *          nickname="listPerson",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="name",
     *                  description="The person name",
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
        $qb->select('Person', 'i')
            ->from('MediaMine\Entity\Common\Person','Person');

        $name = $this->params()->fromQuery('name', null);
        if ($name != null) {
            $qb->where('Person.name LIKE :name');
            $params['name'] = '%' . $name . '%';
        }
        $qb->leftJoin('Person.images', 'i');

        $o = 'ASC';
        $order = $this->params()->fromQuery('order', null);
        if ($order == 'DESC') {
            $o = 'DESC';
        }
        $by = 'name';
        $orderBy = $this->params()->fromQuery('orderBy', null);
        if ($orderBy != null) {
            $by = $orderBy;
        }

        $qb->orderBy('Person.' . $by, $o);
        $resultSet = $qb->setParameters($params)->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return new JsonModel($resultSet);
    }

    /**
     *  @SWG\Api(
     *      path="/person/{id}",
     *      @SWG\Operation(
     *          nickname="getPerson",
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

        $person = $this->getEm()->find('MediaMine\Entity\Common\Person', $id);

        return new JsonModel($person->getArrayCopy());
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
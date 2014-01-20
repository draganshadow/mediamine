<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/directory",
 *      basePath="/api"
 * )
 */
class DirectoryController extends AbstractRestController implements EntityManagerAware
{
    /**
     *  @SWG\Api(
     *      path="/directory",
     *      @SWG\Operation(
     *          nickname="listDirectories",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="parent",
     *                  description="The Order",
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
        $parent = (int) $this->params()->fromQuery('parent', null);
        $options = array('hydrate' => Query::HYDRATE_ARRAY);
        if ($parent) {
            $options['parent'] = $parent;
        } else {
            $options['root'] = true;
        }
        $directories = $this->getRepository('File\Directory')->findFullBy($options);
        return new JsonModel($directories);
    }

    /**
     *  @SWG\Api(
     *      path="/directory/{id}",
     *      @SWG\Operation(
     *          nickname="getDirectory",
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

        $directories = $this->getRepository('File\Directory')->findFullBy($parent);
        return new JsonModel($video);
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
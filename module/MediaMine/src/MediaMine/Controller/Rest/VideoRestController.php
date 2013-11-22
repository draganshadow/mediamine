<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/video",
 *      basePath="/api"
 * )
 */
class VideoRestController extends AbstractRestController implements EntityManagerAware
{
    /**
     *  @SWG\Api(
     *      path="/video",
     *      @SWG\Operation(
     *          nickname="listVideos",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="season",
     *                  description="The Order",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              ),
     *              @SWG\Parameter(
     *                  name="type",
     *                  description="The type",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              ),
     *              @SWG\Parameter(
     *                  name="orderBy",
     *                  description="The type",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              ),
     *              @SWG\Parameter(
     *                  name="order",
     *                  description="The type",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              ),
     *              @SWG\Parameter(
     *                  name="text",
     *                  description="Text",
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
        $limit = 20;
        $page = 0;
        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Video', 'i')
            ->from('MediaMine\Entity\Video\Video','Video')
            ->join('Video.images', 'i');

        $season = (int) $this->params()->fromQuery('season', null);
        if ($season != null) {
            $qb->innerJoin('Video.season', 'season', 'WITH', 'season.id = :season');
            $params['season'] = $season;
        }

        $type = $this->params()->fromQuery('type', null);
        if ($type != null) {
            $qb->innerJoin('Video.type', 'type', 'WITH', 'type.name = :type');
            $params['type'] = $type;
        }

        $text = $this->params()->fromQuery('text', null);
        if ($text != null) {
            $qb->where('Video.name LIKE :text');
            $params['text'] = '%' . $text . '%';
        }

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

        $pageP = (int) $this->params()->fromQuery('page', null);
        if ($pageP != null) {
            $page = $pageP - 1;
        }

        $qb->orderBy('Video.' . $by, $o);
        $resultSet = $qb->setParameters($params)
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonModel($resultSet);
    }

    /**
     *  @SWG\Api(
     *      path="/video/{id}",
     *      @SWG\Operation(
     *          nickname="getVideo",
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

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('video', 'files','f', 'i')
            ->from('MediaMine\Entity\Video\Video','video')
            ->join('video.files', 'files')
            ->join('files.file', 'f')
            ->join('video.images', 'i')
            ->where('video.id = :id')
            ->setParameter('id', $id);
        $videos = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $video = $videos[0];
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
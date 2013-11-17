<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\EntityManagerAware;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/file",
 *      basePath="/api"
 * )
 */
class FileRestController extends AbstractRestController implements EntityManagerAware
{
    /**
     *  @SWG\Api(
     *      path="/file",
     *      @SWG\Operation(
     *          nickname="listFiles",
     *          method="GET",
     *          summary="This is a test",
     *          @SWG\Parameters(
     *              @SWG\Parameter(
     *                  name="season",
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
        $params = array();
        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Video', 'i')
            ->from('MediaMine\Entity\Video\Video','Video')
            ->join('Video.images', 'i');

        $season = (int) $this->params()->fromQuery('season', null);
        if ($season != null) {
            $qb->innerJoin('Video.season', 'season', 'WITH', 'season.id = :season');
            $params['season'] = $season;
        }

        $qb->orderBy('Video.name', 'ASC');
        $resultSet = $qb->setParameters($params)->getQuery()->getResult(Query::HYDRATE_ARRAY);

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
//        $files = array();
//        foreach ($video->files as $f){
//            $files[] = $f->file->id;
//        }
//        $images = array();
//        foreach ($video->images as $i){
//            $images[] = $i->file->id;
//        }

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
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
    public function getList()
    {
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
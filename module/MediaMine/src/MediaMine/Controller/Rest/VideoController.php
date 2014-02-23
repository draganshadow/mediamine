<?php
namespace MediaMine\Controller\Rest;

use Doctrine\ORM\Query;
use MediaMine\Initializer\ElasticsearchAwareInterface;
use Zend\View\Model\JsonModel;

/**
 * @SWG\Resource(
 *      resourcePath="/video",
 *      basePath="/api"
 * )
 */
class VideoController extends AbstractRestController implements ElasticsearchAwareInterface
{
    /**
     * @var
     */
    private $es;

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
     *                  name="genre",
     *                  description="The genre",
     *                  paramType="query",
     *                  required="false",
     *                  type="string"
     *              ),
     *              @SWG\Parameter(
     *                  name="person",
     *                  description="The person",
     *                  paramType="query",
     *                  required="false",
     *                  type="int"
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
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('Video', 'i')
            ->from('MediaMine\Entity\Video\Video','Video')
            ->join('Video.images', 'i');

        $text = $this->params()->fromQuery('text', null);
        if ($text != null) {
            // Define a Query. We want a string query.
            $elasticaQueryString = new \Elastica\Query\QueryString();
            $elasticaQueryString->setQuery((string)$this->replaceAccents($text));

            // Create the actual search object with some data.
            $elasticaQuery = new \Elastica\Query();
            $elasticaQuery->setQuery($elasticaQueryString);

            $resultSet = $this->getElasticsearch()->getIndex('mediamine')->getType('video')->search($elasticaQuery);
            // Get IDs
            $ids = array();
            foreach($resultSet as $result){
                $ids[] = $result->getId();
            }
            $qb->where('Video.id IN (:ids)');
            $params['ids'] = $ids;
        }

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

        $genre = $this->params()->fromQuery('genre', null);
        if ($genre != null) {
            $qb->innerJoin('Video.genres', 'genre', 'WITH', 'genre.name LIKE :genre');
            $params['genre'] = '%' . strtolower($genre) . '%';
        }

        $person = (int) $this->params()->fromQuery('person', null);
        if ($person != null) {
            $qb->innerJoin('Video.staffs', 'Staffs');
            $qb->innerJoin('Staffs.person', 'person', 'WITH', 'person.id = :person');
            $params['person'] = $person;
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

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('video', 'files','f', 'i', 'g', 'staffs', 'r', 'p', 'c')
            ->from('MediaMine\Entity\Video\Video','video')
            ->join('video.files', 'files')
            ->join('files.file', 'f')
            ->leftJoin('video.images', 'i')
            ->leftJoin('video.genres', 'g')
            ->leftJoin('video.staffs', 'staffs')
            ->leftJoin('staffs.role', 'r')
            ->leftJoin('staffs.person', 'p')
            ->leftJoin('staffs.character', 'c')
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

    public function setElasticsearch(\Elastica\Client $es)
    {
        $this->es = $es;
    }

    /**
     * @return \Elastica\Client
     */
    public function getElasticsearch()
    {
        return $this->es;
    }

    protected function replaceAccents($string)
    {
        return str_replace(
            array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),
            array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
            $string);
    }
}
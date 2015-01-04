<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Video\Video;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class VideoController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all videos.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing videos.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="20", description="How many videos to return.")
     * @Annotations\QueryParam(name="type", requirements="\d+", default="20", description="videos type to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getVideosAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = [];
        $type = $request->get('type', false);
        if ($type) {
            $options['type'] = $type;
        }
        $genre = $request->get('genre', false);
        if ($genre) {
            $options['genres'] = [$genre];
        }
        $season = $request->get('season', false);
        if ($season) {
            $options['season'] = [$season];
        }
        $person = $request->get('person', false);
        if ($person) {
            $options['person'] = $person;
        }
        $limit = $request->get('limit', 20);
        if ($limit) {
            $options['limit'] = $limit;
        }
        $page = $request->get('page', 0);
        if ($page) {
            $options['page'] = $page - 1;
        }
        $order = $request->get('order', 0);
        if ($order) {
            $options['order'] = $order;
        }
        $orderBy = $request->get('orderBy', 0);
        if ($orderBy) {
            $options['orderBy'] = $orderBy;
        }
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addFile'] = true;
        $options['addImages'] = true;
        return $this->getRepository('Video\Video')->findFullBy($options);
    }

    /**
     * Get a single video.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Video",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the video is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="video")
     *
     * @param Request $request the request object
     * @param int $id the video id
     *
     * @return array
     *
     * @throws NotFoundHttpException when video not exist
     */
    public function getVideoAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addFile'] = true;
        $options['addImages'] = true;
        $options['addSeason'] = true;
        $options['addGroup'] = true;
        $options['addGenres'] = true;
        $options['addStaffs'] = true;
        return $this->getRepository('Video\Video')->findFullBy($options, true);
    }

    /**
     * Creates a new video from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\VideoType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="video")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postVideoAction(Request $request)
    {
        $entity = $this->getRepository('Video\Video')->create($request->request->all());
        $this->getRepository('Video\Video')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing video from the submitted data or create a new video at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\VideoType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="video")
     *
     * @param Request $request the request object
     * @param int $id the video id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when video not exist
     */
    public function putVideoAction(Request $request, $id)
    {

        $entity = $this->getRepository('Video\Video')->update($id, $request->request->all(), true);
        $this->getRepository('Video\Video')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a video.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the video id
     *
     * @return RouteRedirectView
     */
    public function removeVideoAction(Request $request, $id)
    {
        return $this->deleteVideoAction($request, $id);
    }

    /**
     * Removes a video.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the video id
     *
     * @return RouteRedirectView
     */
    public function deleteVideoAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_videos', array(), Codes::HTTP_NO_CONTENT);
    }
}

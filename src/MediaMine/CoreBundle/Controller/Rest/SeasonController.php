<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Video\Season;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class SeasonController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all seasons.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="group", requirements="\d+", default="1", description="Offset from which to start listing seasons.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many seasons to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSeasonsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = [];
        $group = $request->get('serie', false);
        if ($group) {
            $options['group'] = [$group];
        }
        $order = $request->get('order', 'asc');
        if ($order) {
            $options['order'] = $order;
        }
        $orderBy = $request->get('orderBy', 'name');
        if ($orderBy) {
            $options['orderBy'] = $orderBy;
        }
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addImages'] = true;
        return $this->getRepository('Video\Season')->findFullBy($options);
    }

    /**
     * Get a single season.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Season",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the season is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="season")
     *
     * @param Request $request the request object
     * @param int $id the season id
     *
     * @return array
     *
     * @throws NotFoundHttpException when season not exist
     */
    public function getSeasonAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addGroup'] = true;
        return $this->getRepository('Video\Season')->findFullBy($options, true);
    }

    /**
     * Creates a new season from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\SeasonType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="season")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postSeasonAction(Request $request)
    {
        $entity = $this->getRepository('Video\Season')->create($request->request->all());
        $this->getRepository('Video\Season')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing season from the submitted data or create a new season at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\SeasonType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="season")
     *
     * @param Request $request the request object
     * @param int $id the season id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when season not exist
     */
    public function putSeasonAction(Request $request, $id)
    {

        $entity = $this->getRepository('Video\Season')->update($id, $request->request->all(), true);
        $this->getRepository('Video\Season')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a season.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the season id
     *
     * @return RouteRedirectView
     */
    public function removeSeasonAction(Request $request, $id)
    {
        return $this->deleteSeasonAction($request, $id);
    }

    /**
     * Removes a season.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the season id
     *
     * @return RouteRedirectView
     */
    public function deleteSeasonAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_seasons', array(), Codes::HTTP_NO_CONTENT);
    }
}

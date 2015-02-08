<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class GroupController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all groups.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="page", requirements="\d+", default="1", description="Offset from which to start listing groups.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="20", description="How many groups to return.")
     * @Annotations\QueryParam(name="order", requirements="\d+", default="ASC", description="Order of results")
     * @Annotations\QueryParam(name="orderBy", requirements="\d+", default="name", description="Order field")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getGroupsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = [];
        $page = $request->get('page', 0);
        if ($page) {
            $options['page'] = $page - 1;
        }
        $limit = $request->get('limit', 20);
        if ($limit) {
            $options['limit'] = $limit;
        }
        $order = $request->get('order', 'asc');
        if ($order) {
            $options['order'] = $order;
        }
        $orderBy = $request->get('orderBy', 'name');
        if ($orderBy) {
            $options['orderBy'] = $orderBy;
        }
        $count = $request->get('count');
        if ($count) {
            $options['count'] = $count;
        }
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addImages'] = true;
        return $this->getRepository('Video\Group')->findFullBy($options);
    }

    /**
     * Get a single group.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Group",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the group is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="group")
     *
     * @param Request $request the request object
     * @param int $id the group id
     *
     * @return array
     *
     * @throws NotFoundHttpException when group not exist
     */
    public function getGroupAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Video\Group')->findFullBy($options, true);
    }

    /**
     * Creates a new group from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\GroupType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="group")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postGroupAction(Request $request)
    {
        $entity = $this->getRepository('Video\Group')->create($request->request->all());
        $this->getRepository('Video\Group')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing group from the submitted data or create a new group at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\GroupType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="group")
     *
     * @param Request $request the request object
     * @param int $id the group id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when group not exist
     */
    public function putGroupAction(Request $request, $id)
    {

        $entity = $this->getRepository('Video\Group')->update($id, $request->request->all(), true);
        $this->getRepository('Video\Group')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a group.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the group id
     *
     * @return RouteRedirectView
     */
    public function removeGroupAction(Request $request, $id)
    {
        return $this->deleteGroupAction($request, $id);
    }

    /**
     * Removes a group.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the group id
     *
     * @return RouteRedirectView
     */
    public function deleteGroupAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_groups', array(), Codes::HTTP_NO_CONTENT);
    }
}

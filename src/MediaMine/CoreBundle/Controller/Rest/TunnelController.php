<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\System\Tunnel;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class TunnelController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all tunnels.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing tunnels.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many tunnels to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getTunnelsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Tunnel')->findFullBy($options);
    }

    /**
     * Get a single tunnel.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Tunnel",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the tunnel is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="tunnel")
     *
     * @param Request $request the request object
     * @param int $id the tunnel id
     *
     * @return array
     *
     * @throws NotFoundHttpException when tunnel not exist
     */
    public function getTunnelAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Tunnel')->findFullBy($options, true);
    }

    /**
     * Creates a new tunnel from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\TunnelType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="tunnel")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postTunnelAction(Request $request)
    {
        $entity = $this->getRepository('System\Tunnel')->create($request->request->all());
        $this->getRepository('System\Tunnel')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing tunnel from the submitted data or create a new tunnel at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\TunnelType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="tunnel")
     *
     * @param Request $request the request object
     * @param int $id the tunnel id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when tunnel not exist
     */
    public function putTunnelAction(Request $request, $id)
    {

        $entity = $this->getRepository('System\Tunnel')->update($id, $request->request->all(), true);
        $this->getRepository('System\Tunnel')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a tunnel.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the tunnel id
     *
     * @return RouteRedirectView
     */
    public function removeTunnelAction(Request $request, $id)
    {
        return $this->deleteTunnelAction($request, $id);
    }

    /**
     * Removes a tunnel.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the tunnel id
     *
     * @return RouteRedirectView
     */
    public function deleteTunnelAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_tunnels', array(), Codes::HTTP_NO_CONTENT);
    }
}

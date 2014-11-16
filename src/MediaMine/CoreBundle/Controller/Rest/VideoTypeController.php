<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Video\Type;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class VideoTypeController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all videotypes.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing videotypes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many videotypes to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getVideotypesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Video\Type')->findFullBy($options);
    }

    /**
     * Get a single videotype.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\VideoType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the videotype is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="videotype")
     *
     * @param Request $request the request object
     * @param int $id the videotype id
     *
     * @return array
     *
     * @throws NotFoundHttpException when videotype not exist
     */
    public function getVideotypeAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Video\Type')->findFullBy($options, true);
    }

    /**
     * Creates a new videotype from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\VideoTypeType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="videotype")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postVideotypeAction(Request $request)
    {
        $entity = $this->getRepository('Video\Type')->create($request->request->all());
        $this->getRepository('Video\Type')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing videotype from the submitted data or create a new videotype at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\VideoTypeType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="videotype")
     *
     * @param Request $request the request object
     * @param int $id the videotype id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when videotype not exist
     */
    public function putVideotypeAction(Request $request, $id)
    {

        $entity = $this->getRepository('Video\Type')->update($id, $request->request->all(), true);
        $this->getRepository('Video\Type')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a videotype.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the videotype id
     *
     * @return RouteRedirectView
     */
    public function removeVideotypeAction(Request $request, $id)
    {
        return $this->deleteVideoTypeAction($request, $id);
    }

    /**
     * Removes a videotype.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the videotype id
     *
     * @return RouteRedirectView
     */
    public function deleteVideotypeAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_videotypes', array(), Codes::HTTP_NO_CONTENT);
    }
}

<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Common\Person;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class PersonController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all persons.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing persons.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many persons to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPersonsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Common\Person')->findFullBy($options);
    }

    /**
     * Get a single person.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Person",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the person is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="person")
     *
     * @param Request $request the request object
     * @param int $id the person id
     *
     * @return array
     *
     * @Annotations\Get("/persons/{id}")
     * @throws NotFoundHttpException when person not exist
     */
    public function getPersonAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Common\Person')->findFullBy($options, true);
    }

    /**
     * Creates a new person from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\PersonType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="person")
     *
     * @param Request $request the request object
     *
     * @Annotations\Post("/persons")
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postPersonAction(Request $request)
    {
        $entity = $this->getRepository('Common\Person')->create($request->request->all());
        $this->getRepository('Common\Person')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing person from the submitted data or create a new person at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\PersonType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="person")
     *
     * @param Request $request the request object
     * @param int $id the person id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @Annotations\Put("/persons/{id}")
     * @throws NotFoundHttpException when person not exist
     */
    public function putPersonAction(Request $request, $id)
    {

        $entity = $this->getRepository('Common\Person')->update($id, $request->request->all(), true);
        $this->getRepository('Common\Person')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a person.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the person id
     * @Annotations\Get("/persons/{id}/remove")
     *
     * @return RouteRedirectView
     */
    public function removePersonAction(Request $request, $id)
    {
        return $this->deletePersonAction($request, $id);
    }

    /**
     * Removes a person.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the person id
     *
     * @Annotations\Delete("/persons/{id}")
     * @return RouteRedirectView
     */
    public function deletePersonAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_persons', array(), Codes::HTTP_NO_CONTENT);
    }
}

<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\System\Module;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class ModuleController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all modules.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing modules.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many modules to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getModulesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Module')->findFullBy($options);
    }

    /**
     * Get a single module.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Module",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the module is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="module")
     *
     * @param Request $request the request object
     * @param int $id the module id
     *
     * @return array
     *
     * @throws NotFoundHttpException when module not exist
     */
    public function getModuleAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Module')->findFullBy($options, true);
    }

    /**
     * Creates a new module from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\ModuleType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="module")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postModuleAction(Request $request)
    {
        $entity = $this->getRepository('System\Module')->create($request->request->all());
        $this->getRepository('System\Module')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing module from the submitted data or create a new module at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\ModuleType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="module")
     *
     * @param Request $request the request object
     * @param int $id the module id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when module not exist
     */
    public function putModuleAction(Request $request, $id)
    {

        $entity = $this->getRepository('System\Module')->update($id, $request->request->all(), true);
        $this->getRepository('System\Module')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a module.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the module id
     *
     * @return RouteRedirectView
     */
    public function removeModuleAction(Request $request, $id)
    {
        return $this->deleteModuleAction($request, $id);
    }

    /**
     * Removes a module.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the module id
     *
     * @return RouteRedirectView
     */
    public function deleteModuleAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_modules', array(), Codes::HTTP_NO_CONTENT);
    }
}

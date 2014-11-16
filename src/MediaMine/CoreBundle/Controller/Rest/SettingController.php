<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\System\Setting;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class SettingController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all settings.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="groupKey", nullable=true, description="Group of settings")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSettingsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = array_filter($paramFetcher->all());
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Setting')->findFullBy($options);
    }

    /**
     * Get a single setting.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Setting",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the setting is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="setting")
     *
     * @param Request $request the request object
     * @param int $id the setting id
     *
     * @return array
     *
     * @throws NotFoundHttpException when setting not exist
     */
    public function getSettingAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Setting')->findFullBy($options, true);
    }

    /**
     * Creates a new setting from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\SettingType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="setting")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postSettingAction(Request $request)
    {
        $entity = $this->getRepository('System\Setting')->create($request->request->all());
        $this->getRepository('System\Setting')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing setting from the submitted data or create a new setting at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\SettingType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="setting")
     *
     * @param Request $request the request object
     * @param int $id the setting id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when setting not exist
     */
    public function putSettingAction(Request $request, $id)
    {

        $entity = $this->getRepository('System\Setting')->update($id, $request->request->all(), true);
        $this->getRepository('System\Setting')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a setting.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the setting id
     *
     * @return RouteRedirectView
     */
    public function removeSettingAction(Request $request, $id)
    {
        return $this->deleteSettingAction($request, $id);
    }

    /**
     * Removes a setting.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the setting id
     *
     * @return RouteRedirectView
     */
    public function deleteSettingAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_settings', array(), Codes::HTTP_NO_CONTENT);
    }
}

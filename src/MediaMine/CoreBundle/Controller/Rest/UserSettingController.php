<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\System\UserSetting;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class UserSettingController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * @Inject("%config%")
     */
    public $config;

    /**
     * @Inject("%mediamine%")
     */
    public $mediamine;

    /**
     * List all usersettings.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="groupKey", nullable=true, description="Group of usersettings")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getUserSettingsAction($userId, Request $request, ParamFetcherInterface $paramFetcher)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $options = array_filter($paramFetcher->all());
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $userSettings = $this->getRepository('System\UserSetting')->findFullBy($options);
        $groupedSettings = [];
        foreach ($userSettings as $us) {
            if (!array_key_exists($us['groupKey'], $groupedSettings)) {
                $groupedSettings[$us['groupKey']] = [];
            }
            if (!array_key_exists($us['key'], $groupedSettings[$us['groupKey']])) {
                $groupedSettings[$us['groupKey']][$us['key']] = $us;
            }
        }
        $configUserSettings = $this->mediamine['user_settings'];
        $added = false;
        foreach ($configUserSettings as $gk => $gus) {
            foreach ($gus as $k => $s) {
                if (!array_key_exists($gk, $groupedSettings) || !array_key_exists($k, $groupedSettings[$gk])) {
                    $added = true;
                    $this->getRepository('System\UserSetting')->create([
                        'user' => $user,
                        'groupKey' => $gk,
                        'key' => $k,
                        'value' => $s
                    ]);
                }
            }
        }
        if ($added) {
            $this->getRepository('System\UserSetting')->flush();
            $userSettings = $this->getRepository('System\UserSetting')->findFullBy($options);
        }

        return $userSettings;
    }

    /**
     * Get a single usersetting.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\UserSetting",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the usersetting is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="usersetting")
     *
     * @param Request $request the request object
     * @param int $id the usersetting id
     *
     * @return array
     *
     * @throws NotFoundHttpException when usersetting not exist
     */
    public function getUserSettingAction($userId, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\UserSetting')->findFullBy($options, true);
    }

    /**
     * Creates a new usersetting from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\UserSettingType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="usersetting")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postUserSettingAction($userId, Request $request)
    {
        $entity = $this->getRepository('System\UserSetting')->create($request->request->all());
        $this->getRepository('System\UserSetting')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing usersetting from the submitted data or create a new usersetting at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\UserSettingType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="usersetting")
     *
     * @param Request $request the request object
     * @param int $userId the usersetting id
     * @param int $id the usersetting id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when usersetting not exist
     */
    public function putUserSettingAction($userId, $id, Request $request)
    {

        $entity = $this->getRepository('System\UserSetting')->update($id, $request->request->all(), true);
        $this->getRepository('System\UserSetting')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a usersetting.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the usersetting id
     *
     * @return RouteRedirectView
     */
    public function removeUserSettingAction($userId, $id, Request $request)
    {
        return $this->deleteUserSettingAction($userId, $id, $request);
    }

    /**
     * Removes a usersetting.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the usersetting id
     *
     * @return RouteRedirectView
     */
    public function deleteUserSettingAction($userId, $id, Request $request)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_usersettings', array(), Codes::HTTP_NO_CONTENT);
    }
}

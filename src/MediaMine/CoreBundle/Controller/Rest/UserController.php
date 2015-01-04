<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class UserController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * @Inject("sonata.user.user_manager")
     * @var \Sonata\UserBundle\Entity\UserManager
     */
    protected $userManager;

    /**
     * List all users.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="groupKey", nullable=true, description="Group of users")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getUsersAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        /**
         * @var $user \Application\Sonata\UserBundle\Entity\User
         */
        if($user && in_array(UserInterface::ROLE_SUPER_ADMIN, $user->getRoles())) {
            $qb = $this->getRepository('Application\Sonata\UserBundle\Entity\User', true)
                ->createQueryBuilder('User');
            $qb->orderBy('User.username', 'ASC');
            $q = $qb->getQuery();
            $result = $q->getResult(Query::HYDRATE_ARRAY);
            return $result;
        }
    }

    /**
     * Get a single user.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\User",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the user is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     * @param int $id the user id
     *
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserAction(Request $request, $id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {
            /**
             * @var $user \Application\Sonata\UserBundle\Entity\User
             */
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles()
            ];
        }
    }

    /**
     * Creates a new user from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\UserType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postUserAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        /**
         * @var $user \Application\Sonata\UserBundle\Entity\User
         */
        if($user && in_array(UserInterface::ROLE_SUPER_ADMIN, $user->getRoles())) {
            $newUserData = $request->request->all();
            $newUser = $this->userManager->create();
            $newUser->setEmail($newUserData['email']);
            $newUser->setUsername($newUserData['username']);
            $newUser->setPlainPassword($newUserData['password']);
            $newUser->setEnabled(true);
            $this->userManager->updateUser($newUser);
        }
    }

    /**
     * Update existing user from the submitted data or create a new user at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\UserType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param Request $request the request object
     * @param int $id the user id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function putUserAction(Request $request, $id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        /**
         * @var $user \Application\Sonata\UserBundle\Entity\User
         */
        if($user && in_array(UserInterface::ROLE_SUPER_ADMIN, $user->getRoles())) {
        }
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the user id
     *
     * @return RouteRedirectView
     */
    public function removeUserAction(Request $request, $id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        /**
         * @var $user \Application\Sonata\UserBundle\Entity\User
         */
        if($user && in_array(UserInterface::ROLE_SUPER_ADMIN, $user->getRoles())/* && $user->getId() != $id*/) {
            $userToDelete = $this->userManager->find($id);
            $this->userManager->deleteUser($userToDelete);
        }
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the user id
     *
     * @return RouteRedirectView
     */
    public function deleteUserAction(Request $request, $id)
    {
        return $this->removeUserAction($request, $id);
    }
}

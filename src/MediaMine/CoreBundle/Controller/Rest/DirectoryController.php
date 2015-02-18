<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\File\Directory;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class DirectoryController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all directories.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing directories.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many directories to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getDirectoriesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = [];
        $orderBy = $request->get('orderBy', 'name');
        if ($orderBy) {
            $options['orderBy'] = $orderBy;
        }
        $options['parentDirectory'] = $request->get('parent', null);
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('File\Directory')->findFullBy($options);
    }

    /**
     * Get a single directory.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Directory",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the directory is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="directory")
     *
     * @param Request $request the request object
     * @param int $id the directory id
     *
     * @return array
     *
     * @throws NotFoundHttpException when directory not exist
     */
    public function getDirectoryAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['addParentDirectory'] = true;
        return $this->getRepository('File\Directory')->findFullBy($options, true);
    }

    /**
     * Creates a new directory from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\DirectoryType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="directory")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postDirectoryAction(Request $request)
    {
        $entity = $this->getRepository('File\Directory')->create($request->request->all());
        $this->getRepository('File\Directory')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing directory from the submitted data or create a new directory at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\DirectoryType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="directory")
     *
     * @param Request $request the request object
     * @param int $id the directory id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when directory not exist
     */
    public function putDirectoryAction(Request $request, $id)
    {

        $entity = $this->getRepository('File\Directory')->update($id, $request->request->all(), true);
        $this->getRepository('File\Directory')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a directory.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the directory id
     *
     * @return RouteRedirectView
     */
    public function removeDirectoryAction(Request $request, $id)
    {
        return $this->deleteDirectoryAction($request, $id);
    }

    /**
     * Removes a directory.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the directory id
     *
     * @return RouteRedirectView
     */
    public function deleteDirectoryAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_directories', array(), Codes::HTTP_NO_CONTENT);
    }
}

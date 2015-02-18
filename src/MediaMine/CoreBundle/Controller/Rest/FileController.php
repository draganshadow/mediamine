<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class FileController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * @Inject("%mediamine%")
     */
    public $config;

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
    public function getFilesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options['directory'] = $request->get('directory', null);
        $orderBy = $request->get('orderBy', 'name');
        if ($orderBy) {
            $options['orderBy'] = $orderBy;
        }
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $results = $this->getRepository('File\File')->findFullBy($options);
        foreach ($results as &$r) {
            foreach ($this->config['filetypes'] as $t => $extensions) {
                if (in_array($r['extension'], $extensions)) {
                    $r['type'] = $t;
                }
            }
        }
        return $results;
    }

    /**
     * Get a single file.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\File",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the file is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="file")
     *
     * @param Request $request the request object
     * @param int $id the file id
     *
     * @return array
     *
     * @throws NotFoundHttpException when file not exist
     */
    public function getFileAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('File\File')->findFullBy($options, true);
    }

    /**
     * Creates a new file from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\FileType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="file")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postFileAction(Request $request)
    {
        $entity = $this->getRepository('File\File')->create($request->request->all());
        $this->getRepository('File\File')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing file from the submitted data or create a new file at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\FileType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="file")
     *
     * @param Request $request the request object
     * @param int $id the file id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when file not exist
     */
    public function putFileAction(Request $request, $id)
    {

        $entity = $this->getRepository('File\File')->update($id, $request->request->all(), true);
        $this->getRepository('File\File')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a file.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the file id
     *
     * @return RouteRedirectView
     */
    public function removeFileAction(Request $request, $id)
    {
        return $this->deleteFileAction($request, $id);
    }

    /**
     * Removes a file.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the file id
     *
     * @return RouteRedirectView
     */
    public function deleteFileAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_directories', array(), Codes::HTTP_NO_CONTENT);
    }
}

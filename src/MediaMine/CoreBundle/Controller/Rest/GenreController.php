<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\Video\Genre;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class GenreController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all genres.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing genres.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="20", description="How many genres to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getGenresAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Video\Genre')->findFullBy($options);
    }

    /**
     * Get a single genre.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Genre",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the genre is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="genre")
     *
     * @param Request $request the request object
     * @param int $id the genre id
     *
     * @return array
     *
     * @throws NotFoundHttpException when genre not exist
     */
    public function getGenreAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('Video\Genre')->findFullBy($options, true);
    }

    /**
     * Creates a new genre from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\GenreType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="genre")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postGenreAction(Request $request)
    {
        $entity = $this->getRepository('Video\Genre')->create($request->request->all());
        $this->getRepository('Video\Genre')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing genre from the submitted data or create a new genre at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\GenreType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="genre")
     *
     * @param Request $request the request object
     * @param int $id the genre id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when genre not exist
     */
    public function putGenreAction(Request $request, $id)
    {

        $entity = $this->getRepository('Video\Genre')->update($id, $request->request->all(), true);
        $this->getRepository('Video\Genre')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes a genre.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the genre id
     *
     * @return RouteRedirectView
     */
    public function removeGenreAction(Request $request, $id)
    {
        return $this->deleteGenreAction($request, $id);
    }

    /**
     * Removes a genre.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the genre id
     *
     * @return RouteRedirectView
     */
    public function deleteGenreAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_genres', array(), Codes::HTTP_NO_CONTENT);
    }
}

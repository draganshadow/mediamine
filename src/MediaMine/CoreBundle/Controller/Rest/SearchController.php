<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\AbstractEntity;
use MediaMine\CoreBundle\Entity\Video\Video;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class SearchController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * Search medias
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing videos.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="20", description="How many videos to return.")
     * @Annotations\QueryParam(name="text", requirements="\d+", default=" ", description="text to search")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSearchsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $text = $request->get('text', false);
        $limit = $request->get('limit', 20);
        $page = $request->get('page', 1);

        /**
         * @var $finder \FOS\ElasticaBundle\Finder\TransformedFinder
         */
        $finder = $this->container->get('fos_elastica.finder.search');
        $paginator = $finder->findPaginated($text);
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Get a single result
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Video",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the video is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="video")
     *
     * @param Request $request the request object
     * @param int $id the video id
     *
     * @return array
     *
     * @throws NotFoundHttpException when video not exist
     */
    public function getSearchAction(Request $request, $id)
    {
        /**
         * @var $finder \FOS\ElasticaBundle\Finder\TransformedFinder
         */
        $finder = $this->container->get('fos_elastica.finder.search');
        $result = $finder->find($id, 1);
        return $result->getArrayCopy(1);;
    }

}

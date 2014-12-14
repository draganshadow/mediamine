<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JMS\DiExtraBundle\Annotation\Inject;

class JobController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * List all jobs.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes = {
     * 200 = "Returned when successful"
     * }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing jobs.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="20", description="How many jobs to return.")
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getJobsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $options = $paramFetcher->all();
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $options['orderBy'] = 'id';
        $options['order'] = 'DESC';
        return $this->getRepository('System\Job')->findFullBy($options);
    }

    /**
     * Get a single job.
     *
     * @ApiDoc(
     * output = "Acme\DemoBundle\Model\Job",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 404 = "Returned when the job is not found"
     * }
     * )
     *
     * @Annotations\View(templateVar="job")
     *
     * @param Request $request the request object
     * @param int $id the job id
     *
     * @return array
     *
     * @throws NotFoundHttpException when job not exist
     */
    public function getJobAction(Request $request, $id)
    {
        $options['id'] = $id;
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        return $this->getRepository('System\Job')->findFullBy($options, true);
    }

    /**
     * Creates a new job from the submitted data.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\JobType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="job")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postJobAction(Request $request)
    {
        $entity = $this->getRepository('System\Job')->create($request->request->all());
        $this->getRepository('System\Job')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Update existing job from the submitted data or create a new job at a specific location.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\JobType",
     * statusCodes = {
     * 201 = "Returned when a new resource is created",
     * 204 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\View(templateVar="job")
     *
     * @param Request $request the request object
     * @param int $id the job id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when job not exist
     */
    public function putJobAction(Request $request, $id)
    {

        $entity = $this->getRepository('System\Job')->update($id, $request->request->all(), true);
        $this->getRepository('System\Job')->flush();
        return $entity->getArrayCopy();
    }

    /**
     * Removes all job.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @Annotations\Delete("/jobs/remove-all")
     *
     * @param Request $request the request object
     *
     * @return RouteRedirectView
     */
    public function removeAllJobAction(Request $request)
    {
        $jobs = $this->getRepository('System\Job')->findFullBy([]);
        foreach ($jobs as $job) {
            $this->getEntityManager()->remove($job);
        }
        $this->getEntityManager()->flush();
        return $this->routeRedirectView('get_jobs', array(), Codes::HTTP_NO_CONTENT);
    }

    /**
     * Removes a job.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the job id
     *
     * @return RouteRedirectView
     */
    public function removeJobAction(Request $request, $id)
    {
        return $this->deleteJobAction($request, $id);
    }

    /**
     * Removes a job.
     *
     * @ApiDoc(
     * resource = true,
     * statusCodes={
     * 204="Returned when successful"
     * }
     * )
     *
     * @param Request $request the request object
     * @param int $id the job id
     *
     * @return RouteRedirectView
     */
    public function deleteJobAction(Request $request, $id)
    {
        $this->getEntityManager()->remove($id);
        $this->getEntityManager()->flush();
// There is a debate if this should be a 404 or a 204
// see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
    }
}

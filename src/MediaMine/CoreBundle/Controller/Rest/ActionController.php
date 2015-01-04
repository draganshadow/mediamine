<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Message\System\Job;
use MediaMine\CoreBundle\Service\InstallService;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActionController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;


    /**
     * @Inject("%mediamine%")
     */
    public $mediamine;

    /**
     * @Inject("old_sound_rabbit_mq.job_producer")
     * @var Producer
     */
    public $jobProducer;

    /**
     * @Inject("mediamine.service.module.mediamine.install")
     * @var InstallService
     */
    public $installService;

    /**
     * List all admin action.
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
     * @return array
     */
    public function getActionsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $actions = [];
        foreach ($this->mediamine['actions'] as $name => $action) {
            $actions[] = ["name" => $name];
        }
        return $actions;
    }

    /**
     * Execute an action
     *
     * @ApiDoc(
     * resource = true,
     * input = "Acme\DemoBundle\Form\GroupType",
     * statusCodes = {
     * 200 = "Returned when successful",
     * 400 = "Returned when the form has errors"
     * }
     * )
     *
     * @Annotations\RequestParam(name="action", nullable=false, description="Action to execute")
     *
     * @Annotations\View()
     * @Post("/actions/execute")
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postExecuteAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $a = $paramFetcher->get('action');
        if ($a && array_key_exists($a, $this->mediamine['actions'])) {
            $action = $this->mediamine['actions'][$a];
            if ( 'check' == $a) {
                return $this->installService->check();
            } elseif ( 'createdb' == $a) {
                return $this->installService->createdb();
            } else {
                $job = new Job();
                $job->service = $action['service'];
                $job->groupKey = 'admin';
                $job->key = $a;
                $job->parameters = [];
                $this->jobProducer->publish($job->serialize());
            }
            return ['name' => $a];
        }
        return [];
    }

    public function optionsActionsAction()
    {
        return [];
    }
}

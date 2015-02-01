<?php
namespace MediaMine\CoreBundle\Controller\Rest;


use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\Annotations;
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

class MaintenanceController extends FOSRestController
{
    use EntitityManagerAware;
    use LoggerAware;

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

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
     * @Annotations\View()
     * @Annotations\Get("/maintenance/logs")
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function getLogsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $logs = $skuList = preg_split('/\r\n|\r|\n/', shell_exec('grep error ' . $this->rootDir . '/logs/*'));
        return $logs;
    }

    public function optionsActionsAction()
    {
        return [];
    }
}

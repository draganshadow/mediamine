<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\TunnelService;

/**
 * @Service("mediamine.job.mergeperson")
 * @Tag("monolog.logger", attributes = {"channel" = "MergePersonDataJob"})
 */
class MergePersonDataJob extends BaseJob
{

    /**
     * @Inject("mediamine.service.tunnel")
     * @var TunnelService
     */
    public $tunnelService;

    public function execute(Job $job)
    {
        $this->logger->debug('mediamine.job.mergeperson');
        $nbTask = $this->tunnelService->mapAllPersonData();
        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName()
    {
        return 'mediamine.job.mergeperson';
    }
}
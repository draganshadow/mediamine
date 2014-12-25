<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\TunnelService;
use MediaMine\CoreBundle\Tunnel\Mapper\GroupMapper;

/**
 * @Service("mediamine.job.mergegroup")
 * @Tag("monolog.logger", attributes = {"channel" = "MergeGroupDataJob"})
 */
class MergeGroupDataJob extends BaseJob
{

    /**
     * @Inject("mediamine.mapper.group")
     * @var GroupMapper
     */
    public $groupMapper;

    public function execute(Job $job)
    {
        $this->logger->debug('mediamine.job.mergegroup');
        $nbTask = $this->groupMapper->mapAllGroupData($job);

        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName()
    {
        return 'mediamine.job.mergegroup';
    }
}
<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Tunnel\Mapper\VideoMapper;

/**
 * @Service("mediamine.job.mergevideo")
 * @Tag("monolog.logger", attributes = {"channel" = "MergeVideoDataJob"})
 */
class MergeVideoDataJob extends BaseJob
{
    /**
     * @Inject("mediamine.mapper.video")
     * @var VideoMapper
     */
    public $videoMapper;

    public function execute(Job $job)
    {
        $this->logger->debug('mediamine.job.mergevideo');
        $nbTask = $this->videoMapper->mapAllVideoData($job);
        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName()
    {
        return 'mediamine.job.mergevideo';
    }
}
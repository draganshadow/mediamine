<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Tunnel\Mapper\VideoMapper;

/**
 * @Service("mediamine.job.mergevideo")
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
        $this->videoMapper->mapAllVideoData($job);
    }

    public function getServiceName()
    {
        return 'mediamine.job.mergevideo';
    }
}
<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\Library\VideoLibraryService;

/**
 * @Service("mediamine.job.deduplicatevideo")
 */
class DeDuplicateVideoJob extends BaseJob
{
    /**
     * @Inject("mediamine.service.library.video")
     * @var VideoLibraryService
     */
    public $videoLibraryService;

    public function execute(Job $job)
    {
        $this->logger->debug('mediamine.job.deduplicatevideo');
        $nbTask = $this->videoLibraryService->removeDuplicatesVideosJob($job);
        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName()
    {
        return 'mediamine.job.deduplicatevideo';
    }
}
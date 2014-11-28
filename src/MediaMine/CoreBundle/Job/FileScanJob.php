<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\FileService;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;

/**
 * @Service("mediamine.job.filescan")
 * @Tag("monolog.logger", attributes = {"channel" = "FileScanJob"})
 */
class FileScanJob extends BaseJob {

    /**
     * @Inject("mediamine.service.file")
     * @var FileService
     */
    public $fileService;

    public function execute(Job $job) {
        $this->fileService->scan($job->getParams());
        $this->end($job->getId());
    }

    public function getServiceName() {
        return 'mediamine.job.filescan';
    }
}
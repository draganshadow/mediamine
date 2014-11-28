<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\FileService;
use MediaMine\CoreBundle\Service\InstallService;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;

/**
 * @Service("mediamine.job.scan")
 * @Tag("monolog.logger", attributes = {"channel" = "ScanJob"})
 */
class ScanJob extends BaseJob {

    public function execute(Job $job) {
        $fileScanJob = $this->createSubJob($job, 'filescan', 'mediamine.job.filescan', []);
        $searchXMLJob = $this->createSubJob($job, 'searchxmlvideo', 'mediamine.job.searchxmlvideo', [], $fileScanJob);
        $mergeVideoJob = $this->createSubJob($job, 'mergevideo', 'mediamine.job.mergevideo', [], $searchXMLJob);
        $searchXMLGroupJob = $this->createSubJob($job, 'searchxmlgroup', 'mediamine.job.searchxmlgroup', [], $mergeVideoJob);
        $mergeGroupJob = $this->createSubJob($job, 'mergegroup', 'mediamine.job.mergegroup', [], $searchXMLGroupJob);
    }

    public function getServiceName() {
        return 'mediamine.job.scan';
    }
}
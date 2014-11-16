<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\FileService;
use MediaMine\CoreBundle\Service\InstallService;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;

/**
 * @Service("mediamine.job.scan")
 */
class ScanJob extends BaseJob {

    public function execute(Job $job) {
        $fileScanJob = $this->createSubJob($job, 'mediamine.job.filescan');
        $searchXMLJob = $this->createSubJob($job, 'mediamine.job.searchxmlvideo');
        $mergeVideoJob = $this->createSubJob($job, 'mediamine.job.mergevideo');
        $searchXMLJob = $this->createSubJob($job, 'mediamine.job.searchxmlgroup');
        $mergeGroupJob = $this->createSubJob($job, 'mediamine.job.mergegroup');
    }

    public function getServiceName() {
        return 'mediamine.job.scan';
    }
}
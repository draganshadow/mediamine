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
 * @Service("mediamine.job.reset")
 */
class ResetJob extends BaseJob {

    /**
     * @Inject("mediamine.service.module.mediamine.install")
     * @var InstallService
     */
    public $installService;

    public function execute(Job $job) {
        $this->installService->reset();
    }

    public function getServiceName() {
        return 'mediamine.job.reset';
    }
}
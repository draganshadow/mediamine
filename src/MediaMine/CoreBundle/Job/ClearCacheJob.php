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
use Symfony\Component\Process\Process;

/**
 * @Service("mediamine.job.clearcache")
 */
class ClearCacheJob extends BaseJob {

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    public function execute(Job $job) {
        $process = new Process('rm -rf ' . $this->rootDir . '/../web/stream/*');
        $process->run();
        $process = new Process('rm -rf ' . $this->rootDir . '/../web/images/resized/library/*');
        $process->run();
        $process = new Process('rm -rf ' . $this->rootDir . '/../web/images/resized/template/*');
        $process->run();
        $this->end($job->getId());
    }

    public function getServiceName() {
        return 'mediamine.job.reset';
    }
}
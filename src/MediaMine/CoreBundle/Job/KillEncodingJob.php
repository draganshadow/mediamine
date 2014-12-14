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
 * @Service("mediamine.job.killencoding")
 */
class KillEncodingJob extends BaseJob {

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    public function execute(Job $job) {
        $process = new Process('pkill -f "avconv"');
        $process->run();
        $process = new Process('pkill -f "ffmpeg"');
        $process->run();
        $streams = $this->redis->hGetAll('stream');
        foreach ($streams as $s => $v) {
            $process = new Process('rm -rf ' . $this->rootDir . '/../web/stream/' . $s);
            $process->run();
        }
        $this->end($job->getId());
    }

    public function getServiceName() {
        return 'mediamine.job.reset';
    }
}
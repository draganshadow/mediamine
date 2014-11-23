<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\FileService;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Tunnel\XML\XMLTunnel;

/**
 * @Service("mediamine.job.searchxmlvideo")
 */
class SearchXMLVideoJob extends BaseJob {

    /**
     * @Inject("mediamine.tunnel.xmltunnel")
     * @var XMLTunnel
     */
    public $xmlTunnel;

    public function execute(Job $job) {
        $this->logger->debug('mediamine.job.searchxmlvideo');
        $nbTask = $this->xmlTunnel->checkVideos($job);
        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName() {
        return 'mediamine.job.searchxmlvideo';
    }
}
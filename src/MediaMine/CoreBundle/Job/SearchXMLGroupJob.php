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
use MediaMine\CoreBundle\Tunnel\XML\XMLTunnel;

/**
 * @Service("mediamine.job.searchxmlgroup")
 * @Tag("monolog.logger", attributes = {"channel" = "SearchXMLGroupJob"})
 */
class SearchXMLGroupJob extends BaseJob {

    /**
     * @Inject("mediamine.tunnel.xmltunnel")
     * @var XMLTunnel
     */
    public $xmlTunnel;

    public function execute(Job $job) {
        $this->logger->debug('mediamine.job.searchxmlgroup');
        $nbTask = $this->xmlTunnel->checkGroups($job);
        if (!$nbTask) {
            $this->end($job->getId());
        }
    }

    public function getServiceName() {
        return 'mediamine.job.searchxmlgroup';
    }
}
<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\File\File;
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
        $params = $job->getParams();
        if (array_key_exists('files', $params)) {
            $files = $this->getRepository('File\File')->findFullBy([
                'id' => $params['files'],
                'addDirectory' => true,
            ]);
            foreach ($files as $file) {
                /**
                 * @var $file File
                 */
                $this->fileService->update($file->directory);
            }
        }
        $this->fileService->scan([]);
        $this->end($job->getId());
    }

    public function getServiceName() {
        return 'mediamine.job.filescan';
    }
}
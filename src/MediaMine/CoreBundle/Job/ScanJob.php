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
        $fileIdList = [];
        $videoIdList = [];
        $groupIdList = [];

        $params = $job->getParams();
        $subParams = [];
        if (array_key_exists('videos', $params)) {
            $videos = $this->getRepository('Video\Video')->findFullBy([
                'id' => $params['videos'],
                'addFile' => true,
                'addGroup' => true,
                'hydrate' => Query::HYDRATE_ARRAY
            ]);

            foreach ($videos as $video) {
                if (array_key_exists('files', $video) && $video['files']) {
                    foreach ($video['files'] as $file) {
                        $fileIdList[] = $file['file']['id'];
                    }
                }
                $videoIdList[] = $video['id'];
                if (array_key_exists('group', $video) && $video['group']) {
                    $groupIdList[] = $video['group']['id'];
                }
            }
        }
        if (count($videoIdList)) {
            $subParams['videos'] = $videoIdList;
        }
        if (count($fileIdList)) {
            $subParams['files'] = $fileIdList;
        }
        if (count($groupIdList)) {
            $subParams['groups'] = $groupIdList;
        }
        $fileScanJob = $this->createSubJob($job, 'filescan', 'mediamine.job.filescan', $subParams);
        $searchXMLJob = $this->createSubJob($job, 'searchxmlvideo', 'mediamine.job.searchxmlvideo', $subParams, $fileScanJob);
        $mergeVideoJob = $this->createSubJob($job, 'mergevideo', 'mediamine.job.mergevideo', $subParams, $searchXMLJob);
        $searchXMLGroupJob = $this->createSubJob($job, 'searchxmlgroup', 'mediamine.job.searchxmlgroup', $subParams, $mergeVideoJob);
        $mergeGroupJob = $this->createSubJob($job, 'mergegroup', 'mediamine.job.mergegroup', $subParams, $searchXMLGroupJob);
        $deduplicatevideoJob = $this->createSubJob($job, 'deduplicatevideo', 'mediamine.job.deduplicatevideo', $subParams, $mergeGroupJob);
        $deduplicategroupJob = $this->createSubJob($job, 'deduplicategroup', 'mediamine.job.deduplicategroup', $subParams, $deduplicatevideoJob);
        $deduplicateseasonJob = $this->createSubJob($job, 'deduplicateseason', 'mediamine.job.deduplicateseason', $subParams, $deduplicategroupJob);
    }

    public function getServiceName() {
        return 'mediamine.job.scan';
    }
}
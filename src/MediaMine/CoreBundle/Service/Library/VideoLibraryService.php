<?php
namespace MediaMine\CoreBundle\Service\Library;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Entity\Video\Video;
use MediaMine\CoreBundle\Entity\Video\VideoFile;
use MediaMine\CoreBundle\Service\AbstractService;
use MediaMine\CoreBundle\Service\TaskService;

/**
 * @Service("mediamine.service.library.video")
 */
class VideoLibraryService extends AbstractService
{
    /**
     * @Inject("mediamine.service.task")
     * @var TaskService
     */
    public $taskService;

    public function removeDuplicatesJob(Job $job) {
        $params = $job->getParams();
        $nbTasks = 0;
        if (array_key_exists('videos', $params)) {
            foreach ($params['videos'] as $videoId) {
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesTask', ['id' => $videoId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Video')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $video = $row[0];
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesTask', ['id' => $video['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function removeDuplicatesTask($param) {
        $id = $param['id'];
        /**
         * @var $video Video
         */
        $video = $this->getRepository('Video\Video')->findFullBy([
            'id' => $id,
            'addFile'      => true,
            'addDirectory' => true,
            'addStaffs' => true,
            'addImages' => true
        ], true);
        /**
         * @var $similarVideos Video[]
         */
        $similarVideos = $this->getRepository('Video\Video')->findFullBy([
            'originalName' => $video->originalName,
            'year' => $video->year,
            'addFile'      => true,
            'addDirectory' => true
        ]);

        foreach ($similarVideos as $sv) {
            if ($sv->id != $video->id) {
                foreach ($sv->getFiles() as $f) {
                    /**
                     * @var $f VideoFile
                     */
                    $found = false;
                    foreach ($video->getFiles() as $vf) {
                        if ($f->getFile()->getId() == $vf->getFile()->getId()) {
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $this->getRepository('Video\VideoFile')->create(array('video' => $video, 'file' => $f->getFile()));
                    }
                }
                $this->getEntityManager()->persist($video);
                $this->getRepository('Video\Video')->remove($sv);
            }
        }
        $this->getRepository('Video\Video')->flush();
    }
}

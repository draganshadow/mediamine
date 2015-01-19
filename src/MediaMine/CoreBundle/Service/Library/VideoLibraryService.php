<?php
namespace MediaMine\CoreBundle\Service\Library;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Entity\Video\Season;
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

    public function removeDuplicatesVideosJob(Job $job)
    {
        $params = $job->getParams();
        $nbTasks = 0;
        if (array_key_exists('videos', $params)) {
            foreach ($params['videos'] as $videoId) {
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesVideosTask', ['id' => $videoId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Video')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $video = $row[0];
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesVideosTask', ['id' => $video['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function removeDuplicatesSeasonsJob(Job $job)
    {
        $params = $job->getParams();
        $nbTasks = 0;
        if (array_key_exists('seasons', $params)) {
            foreach ($params['seasons'] as $seasonId) {
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesSeasonsTask', ['id' => $seasonId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Group')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $season = $row[0];
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesSeasonsTask', ['id' => $season['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function removeDuplicatesGroupsJob(Job $job)
    {
        $params = $job->getParams();
        $nbTasks = 0;
        if (array_key_exists('groups', $params)) {
            foreach ($params['groups'] as $groupId) {
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesGroupsTask', ['id' => $groupId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Group')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $group = $row[0];
                $this->taskService->createTask($job, 'mediamine.service.library.video', 'removeDuplicatesGroupsTask', ['id' => $group['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function removeDuplicatesVideosTask($param)
    {
        $id = $param['id'];
        /**
         * @var $video Video
         */
        $video = $this->getRepository('Video\Video')->findFullBy([
            'id'           => $id,
            'addFile'      => true,
            'addDirectory' => true,
            'addStaffs'    => true,
            'addImages'    => true
        ], true);
        /**
         * @var $similarVideos Video[]
         */
        $similarVideos = $this->getRepository('Video\Video')->findFullBy([
            'originalName' => $video->originalName,
            'year'         => $video->year,
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

    public function removeDuplicatesSeasonsTask($param)
    {
        $id = $param['id'];
        /**
         * @var $season Season
         */
        $season = $this->getRepository('Video\Season')->findFullBy([
            'id'        => $id,
            'addGroup'  => true
        ], true);

        /**
         * @var $similarSeasons Season[]
         */
        $similarSeasons = $this->getRepository('Video\Season')->findFullBy([
            'number' => $season->number,
            'group'  => $season->group->id
        ]);

        foreach ($similarSeasons as $ss) {
            if ($ss->id != $season->id) {
                $episodes = $this->getRepository('Video\Video')->findFullBy([
                    'season' => $ss->id
                ]);
                foreach ($episodes as $e) {
                    $e->season = $season;
                    $this->getEntityManager()->persist($e);
                }
                $this->getEntityManager()->persist($season);
                $this->getRepository('Video\Season')->remove($ss);
            }
        }
        $this->getRepository('Video\Season')->flush();
    }

    public function removeDuplicatesGroupsTask($param)
    {
        $id = $param['id'];
        /**
         * @var $group Group
         */
        $group = $this->getRepository('Video\Group')->findFullBy([
            'id'        => $id
        ], true);

        /**
         * @var $similarGroups Group[]
         */
        $similarGroups = $this->getRepository('Video\Group')->findFullBy([
            'originalName' => $group->originalName
        ]);

        foreach ($similarGroups as $sg) {
            if ($sg->id != $group->id) {
                $seasons = $this->getRepository('Video\Season')->findFullBy([
                    'group'  => $sg->id
                ]);
                foreach ($seasons as $s) {
                    $s->group = $group;
                    $this->getEntityManager()->persist($s);
                }
                $this->getEntityManager()->persist($group);
                $this->getRepository('Video\Group')->remove($sg);
            }
        }
        $this->getRepository('Video\Group')->flush();
    }
}

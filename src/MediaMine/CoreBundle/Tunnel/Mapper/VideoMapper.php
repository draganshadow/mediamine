<?php

namespace MediaMine\CoreBundle\Tunnel\Mapper;
use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Entity\Video\StaffRole;
use MediaMine\CoreBundle\Entity\Video\Video;


/**
 * @Service("mediamine.mapper.video")
 * @Tag("monolog.logger", attributes = {"channel" = "VideoMapper"})
 */
class VideoMapper extends AbstractMapper {

    public function mapAllVideoData(Job $job)
    {
        $this->clear();
        $this->loadGroups();
        $this->loadSeasons();
        $this->loadGenres();
        $this->loadCountries();
        $params = $job->getParams();
        $nbTasks = 0;
        if (array_key_exists('videos', $params)) {
            foreach ($params['videos'] as $videoId) {
                $this->taskService->createTask($job, 'mediamine.mapper.video', 'mapVideoData', ['id' => $videoId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Video')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $video = $row[0];
                $this->taskService->createTask($job, 'mediamine.mapper.video', 'mapVideoData', ['id' => $video['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }


    public function mapVideoData($param)
    {
        $id = $param['id'];
        $t1 = microtime(true);
        $video = $this->getRepository('Video\Video')->findFullBy([
            'id' => $id,
            'addFile'      => true,
            'addDirectory' => true,
            'addStaffs' => true,
            'addImages' => true,

//            'hydrate' => Query::HYDRATE_ARRAY
        ], true);
        /**
         * @var $video \MediaMine\CoreBundle\Entity\Video\Video
         */
        $name = $video->name;

        $settings = $this->getSettingService()->getSetting('tunnel', 'video');

        /**
         * @var $videoDocument \MediaMine\CoreBundle\Document\Video\Video
         */
        $videoDocument = $this->getMongoRepository('Video\Video')->findOneBy(['videoRef' => (string)$video->id]);

        if ($videoDocument) {
            $tunnelVideos = $videoDocument->getTunnels();
            if (count($tunnelVideos)) {
                $tvs = array();
                foreach ($tunnelVideos as $tv) {
                    /**
                     * @var $tv \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
                     */
                    $tvs[$tv->getTunnel()] = $tv;
                }
                $override = true;
                foreach ($settings as $t) {
                    /**
                     * @var $tv \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
                     */
                    $tv = $tvs[$t];
                    $this->applyVideoTunnelData($tv->getData(), $video, $override);
                    $override = false;
                }
            }

            $t2 = microtime(true);
            $t = $t2 - $t1;
            $this->getLogger()->info('map video data : ' . $name . '->' . $t . ' ms');
        }
    }


    protected function applyVideoTunnelData($data, Video $video, $override = false)
    {
        $images = array();
        if (array_key_exists('images', $data)) {
            foreach($data['images'] as $image) {
                $video->addImageUnique($this->getEntityManager()->getReference('\MediaMine\CoreBundle\Entity\File\File', $image[0]));
            }
        }


        $tunnelData = array(
            'name'         => array_key_exists('name', $data) ? $data['name'] : null,
            'originalName' => array_key_exists('originalName', $data) ? $data['originalName'] : null,
            'summary'      => array_key_exists('summary', $data) ? $data['summary'] : null,
            'year'         => array_key_exists('year', $data) ? (int)$data['year'] : null,
            'episode'      => array_key_exists('episode', $data) ? (int)$data['episode'] : null,
//            'rating'        => $data->rating,
//            'review'        => $data->review,
            'type'         => array_key_exists('type', $data) ? $data['type'] : null,
            'country'         => array_key_exists('country', $data) ? $this->getCreateCountry($data['country']) : null,
        );


        $genres = array_key_exists('genres', $data) ? $this->getCreateGenres($data['genres']) : [];
        foreach ($genres as $genre) {
            $video->addGenreUnique($genre);
        }

        if (array_key_exists('group', $data)) {
            if (count($video->files)) {
                /**
                 * @var $video Video
                 */
                $seasonDirectory = $video->files[0]->file->directory;
                $groupDirectory = $seasonDirectory->parentDirectory;
                $group = $this->getCreateGroup($data['group'], $groupDirectory);
                $video->setGroup($group);
                if (array_key_exists('season', $data)) {
                    $season = $this->getCreateSeason($group, $data['season'], $seasonDirectory);
                    $video->setSeason($season);
                }
            }
        }

        $getName = function ($pa) {
            return substr($pa['name'], 0, 255);
        };

        $personNames = array_unique(
            array_filter(array_merge(
                $data['staffs']['directors'],
                $data['staffs']['writers'],
                array_map($getName, $data['staffs']['actors'])))
        );
        $persons = $this->getCreatePersons($personNames);

        $getRole = function ($pa) {
            return substr($pa['role'], 0, 255);
        };

        $characterNames = array_unique(
            array_merge(array_map($getRole, $data['staffs']['actors']))
        );
        $characters = $this->getCreateCharacters($video, $characterNames);
        $staffs = $video->getStaffByRole();

        if (is_array($video->staffs)) {
            foreach ($video->staffs as $staff) {
                if (!array_key_exists($staff->person->name, $persons)) {
                    $this->getEntityManager()->remove($staff);
                }
            }
        }

        foreach ($data['staffs']['directors'] as $name) {
            if (array_key_exists($name, $persons) && (!array_key_exists('director', $staffs) || !array_key_exists($name, $staffs['director']))) {
                $this->getCreateStaff($video, $persons[$name], StaffRole::DIRECTOR);
            }
        }
        foreach ($data['staffs']['writers'] as $name) {
            if (array_key_exists($name, $persons) && (!array_key_exists('writer', $staffs) || !array_key_exists($name, $staffs['writer']))) {
                $this->getCreateStaff($video, $persons[$name], StaffRole::WRITER);
            }
        }
        foreach ($data['staffs']['actors'] as $actor) {
            if (array_key_exists($actor['name'], $persons)
                && array_key_exists('role', $actor) && !empty($actor['role'])
                && (!array_key_exists('actor', $staffs) || !array_key_exists($actor['name'], $staffs['actor']))
            ) {
                $this->getCreateStaff($video, $persons[$actor['name']], StaffRole::ACTOR, $characters[$actor['role']]);
            }
        }

        /**
         * @var $video Video
         */
        $video = $this->getRepository('Video\Video')->exchangeArrayNoEmpty($tunnelData, $video);
        $this->getEntityManager()->persist($video);
        $this->getEntityManager()->flush();
    }
} 
<?php
namespace MediaMine\CoreBundle\Tunnel\XML;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Document\Tunnel\TunnelGroup;
use MediaMine\CoreBundle\Document\Tunnel\TunnelVideo;
use MediaMine\CoreBundle\Document\Tunnel\XMLVideo;
use MediaMine\CoreBundle\Document\Video\Group;
use MediaMine\CoreBundle\Document\Video\Video;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Service\TaskService;
use MediaMine\CoreBundle\Tunnel\AbstractTunnel;
use MediaMine\CoreBundle\Tunnel\XML\Parser\EpisodeParser;
use MediaMine\CoreBundle\Tunnel\XML\Parser\MovieParser;
use MediaMine\CoreBundle\Tunnel\XML\Parser\SerieParser;

/**
 * @Service("mediamine.tunnel.xmltunnel")
 */
class XMLTunnel extends AbstractTunnel
{
    const KEY = 'xml-tunnel';

    /**
     * @Inject("%mediamine%")
     */
    public $mediamine;

    /**
     * @Inject("mediamine.service.task")
     * @var TaskService
     */
    public $taskService;

    protected $movieParser;
    protected $serieParser;
    protected $episodeParser;

    /**
     * Return array of handled entities and fields
     * @return array
     */
    public function getAbilities()
    {
        return array(
            'Video' => array()
        );
    }

    public function enableTunnel()
    {
        $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');
        $result = $cronRepository->findBy(array('key' => 'XMLTunnelCheckData'));
        if (!count($result)) {
            return array('error' => 1);
        }
        $cron = $result[0];
        $cron->active = true;
        $this->getEntityManager()->persist($cron);
        $this->batch(1);
    }

    public function disableTunnel()
    {
        $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');
        $result = $cronRepository->findBy(array('key' => 'XMLTunnelCheckData'));
        if (!count($result)) {
            return array('error' => 1);
        }
        $cron = $result[0];
        $cron->active = false;
        $this->getEntityManager()->persist($cron);
        $this->batch(1);
    }

    public function checkVideos(Job $job)
    {
        $this->loadOptions();
        $params = $job->getParams();
        $nbTasks = 0;

        if (array_key_exists('videos', $params)) {
            foreach ($params['videos'] as $videoId) {
                $this->taskService->createTask($job, 'mediamine.tunnel.xmltunnel', 'processVideo', ['id' => $videoId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $qb = $this->getRepository('Video\Video')->createBaseQueryBuilder();
            if (!array_key_exists('override', $this->options)) {
                $qb->where('Video.id NOT IN (:xmlTunnelVideos)');
            }
            $iterableResult = $this->getRepository('Video\Video')->findFullBy($params, 2, false, $qb, ['xmlTunnelVideos' => [0]]);
            foreach ($iterableResult as $row) {
                $video = $row[0];
                $this->taskService->createTask($job, 'mediamine.tunnel.xmltunnel', 'processVideo', ['id' => $video['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function checkSeason()
    {

    }

    public function checkGroups(Job $job)
    {
        $this->loadOptions();
        $params = $job->getParams();
        $nbTasks = 0;

        if (array_key_exists('groups', $params)) {
            foreach ($params['groups'] as $groupId) {
                $this->taskService->createTask($job, 'mediamine.tunnel.xmltunnel', 'processGroup', ['id' => $groupId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $qb = $this->getRepository('Video\Group')->createBaseQueryBuilder();
            if (!array_key_exists('override', $this->options)) {
                $qb->where('VGroup.id NOT IN (:xmlTunnelGroups)');
            }
            $iterableResult = $this->getRepository('Video\Group')->findFullBy($params, 2, false, $qb, ['xmlTunnelGroups' => [0]]);
            foreach ($iterableResult as $row) {
                $group = $row[0];
                $this->taskService->createTask($job, 'mediamine.tunnel.xmltunnel', 'processGroup', ['id' => $group['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function processVideo($param)
    {
        $movie = $this->searchMovie($param['id']);
        if (!$movie) {
            $this->searchEpisode($param['id']);
        }
    }

    public function searchMovie($id)
    {
        $video = $this->getRepository('Video\Video')->findFullBy(
            [
                'id'           => $id,
                'addFile'      => true,
                'addDirectory' => true,
                'hydrate'      => Query::HYDRATE_ARRAY
            ], true);

        if (!count($video['files'])) {
            return false;
        }
        $directory = $video['files'][0]['file']['directory'];
        $files = $this->getRepository('File\File')->findFullBy([
            'name'      => 'movie',
            'extension' => 'xml',
            'directory' => $directory['id'],
            'hydrate'   => Query::HYDRATE_ARRAY
        ]);
        if (count($files)) {

            /**
             * @var $movieXml \MediaMine\Core\Entity\File\File
             */
            $movieXml = $files[0];
            $movieMeta = $this->getMovieParser()->parse($directory['path'] . '/' . $movieXml['name'] . '.' . $movieXml['extension']);
            $movieMeta['image'] = 'folder.jpg';


            $images = $this->getRepository('File\File')->findFullBy([
                'name'      => 'folder',
                'extension' => 'jpg',
                'directory' => $directory['id'],
                'hydrate'   => Query::HYDRATE_ARRAY
            ]);
            /**
             * @var $image File
             */
            $image = count($images) ? $images[0] : null;
            $staff['actors'] = $movieMeta['persons'];
            $staff['directors'] = is_array($movieMeta['directors']) ? $movieMeta['directors'] : array($movieMeta['directors']);
            $staff['writers'] = is_array($movieMeta['writers']) ? $movieMeta['writers'] : array($movieMeta['writers']);

            $videoDocument = $this->getMongoRepository('Video\Video')->findOneBy(['videoRef' => (string)$video['id']]);

            if (!$videoDocument) {
                $videoDocument = new Video();
                $videoDocument->setVideoRef((string)$id);
            }
            /**
             * @var $mongoVideo \MediaMine\CoreBundle\Document\Video\Video
             */
            $tunnelVideos = $videoDocument->getTunnels();
            /**
             * @var $tunnelVideo \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
             */
            $tunnelVideo = null;
            $newTunnel = true;
            foreach ($tunnelVideos as $tv) {
                /**
                 * @var $tv \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
                 */
                if ($this->getTunnelName() === $tv->getTunnel()) {
                    $tunnelVideo = $tv;
                    $videoDocument->removeTunnel($tunnelVideo);
                    $newTunnel = false;
                    break;
                }
            }
            if ($newTunnel) {
                $tunnelVideo = new TunnelVideo();
                $tunnelVideo->setTunnel($this->getTunnelName());
            }
            $images = [];
            if ($image) {
                $images[] = [$image['id'], $image['pathKey']];
            }
            $tunnelVideo->setData([
                'name'         => $movieMeta['name'] ? $movieMeta['name'] : $video->name,
                'originalName' => $movieMeta['originalName'],
                'summary'      => $movieMeta['summary'],
                'year'         => $movieMeta['productionYear'],
//                'episode' => $movieMeta['name'],
//                'group' => $movieMeta['name'],
//                'season' => $movieMeta['name'],
                'country'      => $movieMeta['country'],
                'rating'       => $movieMeta['rating'],
//                'review' => $movieMeta['name'],
                'type'         => 'movie',
                'genres'       => $movieMeta['genres'],
                'staffs'       => $staff,
                'images'       => $images,
//                'dataLanguage' => $movieMeta['name'],
            ]);

            $videoDocument->addTunnel($tunnelVideo);

            $this->getMongoManager()->persist($videoDocument);
            $this->getMongoManager()->flush();
            return true;
        } else {
            return false;
        }
    }

    protected function getMovieParser()
    {
        if (null === $this->movieParser) {
            $this->movieParser = new MovieParser();
        }
        return $this->movieParser;
    }

    /**
     * Return tunnel name
     * @return string
     */
    public function getTunnelName()
    {
        return self::KEY;
    }

    public function searchEpisode($id)
    {
        $video = $this->getRepository('Video\Video')->findFullBy(
            [
                'id'           => $id,
                'addFile'      => true,
                'addDirectory' => true,
                'foreignKeys'  => true,
                'hydrate'      => Query::HYDRATE_ARRAY
            ], true);

        if (!count($video['files'])) {
            return false;
        }
        //Assume
        $seasonDirectory = $video['files'][0]['file']['directory'];
        try {
            $serieDirectory = $this->getRepository('File\Directory')->findFullBy([
                'id'      => $seasonDirectory['parent_directory_ref'],
                'hydrate' => Query::HYDRATE_ARRAY
            ], true);

            $seriesXml = $this->getRepository('File\File')->findFullBy([
                'name'         => 'series',
                'extension'    => 'xml',
                'directory'    => $serieDirectory['id'],
                'addDirectory' => true,
                'hydrate'      => Query::HYDRATE_ARRAY
            ], true);
            $episodeXml = $this->getRepository('File\File')->findFullBy([
                'name'         => $video['files'][0]['file']['name'],
                'addDirectory' => true,
                'extension'    => 'xml',
                'hydrate'      => Query::HYDRATE_ARRAY
            ], true);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }

        $seriesMeta = $this->getSerieParser()->parse($seriesXml['directory']['path'] . '/' . $seriesXml['name'] . '.' . $seriesXml['extension']);
        $episodeMeta = $this->getEpisodeParser()->parse($episodeXml['directory']['path'] . '/' . $episodeXml['name'] . '.' . $episodeXml['extension']);

        $images = $this->getRepository('File\File')->findFullBy([
            'name'      => substr($episodeMeta['image'], 0, strrpos($episodeMeta['image'], '.')),
            'extension' => 'jpg',
            'directory' => $episodeXml['directory']['id'],
            'hydrate'   => Query::HYDRATE_ARRAY
        ]);

        $image = count($images) ? $images[0] : null;

        $staff['actors'] = array_key_exists('persons', $episodeMeta) ? $episodeMeta['persons'] : array();
        $staff['directors'] = is_array($episodeMeta['directors']) ? $episodeMeta['directors'] : array($episodeMeta['directors']);
        $staff['writers'] = is_array($episodeMeta['writers']) ? $episodeMeta['writers'] : array($episodeMeta['writers']);

        $videoDocument = $this->getMongoRepository('Video\Video')->findOneBy(['videoRef' => (string)$video['id']]);

        if (!$videoDocument) {
            $videoDocument = new Video();
            $videoDocument->setVideoRef((string)$id);
        }
        /**
         * @var $mongoVideo \MediaMine\CoreBundle\Document\Video\Video
         */
        $tunnelVideos = $videoDocument->getTunnels();
        /**
         * @var $tunnelVideo \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
         */
        $tunnelVideo = null;
        $newTunnel = true;
        foreach ($tunnelVideos as $tv) {
            /**
             * @var $tv \MediaMine\CoreBundle\Document\Tunnel\TunnelVideo
             */
            if ($this->getTunnelName() === $tv->getTunnel()) {
                $tunnelVideo = $tv;
                $videoDocument->removeTunnel($tunnelVideo);
                $newTunnel = false;
                break;
            }
        }
        if ($newTunnel) {
            $tunnelVideo = new TunnelVideo();
            $tunnelVideo->setTunnel($this->getTunnelName());
        }
        $images = [];
        if ($image) {
            $images[] = [$image['id'], $image['pathKey']];
        }
        $tunnelVideo->setData([
            'name'    => $episodeMeta['name'],
//                'originalName' => $episodeMeta['originalName'],
            'summary' => $episodeMeta['summary'],
//                'year' => $episodeMeta['productionYear'],
            'episode' => $episodeMeta['number'],
            'group'   => $seriesMeta['name'],
            'season'  => $episodeMeta['season'],
//                'country' => $episodeMeta['country'],
            'rating'  => $episodeMeta['rating'],
//                'review' => $movieMeta['name'],
            'type'    => 'series',
//                'genres' => $episodeMeta['genres'],
            'staffs'  => $staff,
            'images'  => $images,
//                'dataLanguage' => $movieMeta['name'],
        ]);

        $videoDocument->addTunnel($tunnelVideo);

        $this->getMongoManager()->persist($videoDocument);
        $this->getMongoManager()->flush();
        return true;
    }

    protected function getSerieParser()
    {
        if (null === $this->serieParser) {
            $this->serieParser = new SerieParser();
        }
        return $this->serieParser;
    }

    protected function getEpisodeParser()
    {
        if (null === $this->episodeParser) {
            $this->episodeParser = new EpisodeParser();
        }
        return $this->episodeParser;
    }

    public function processGroup($param)
    {
        $group = $this->searchGroup($param['id']);
    }

    public function searchGroup($id)
    {

        $group = $this->getRepository('Video\Group')->findFullBy(
            [
                'id'           => $id,
                'addDirectory' => true,
                'foreignKeys'  => true,
                'hydrate'      => Query::HYDRATE_ARRAY
            ], true);

        if (!count($group['directory'])) {
            return false;
        }
        $serieXml = [];
        try {
            $serieXml = $this->getRepository('File\File')->findFullBy(
                array(
                    'name'         => 'series',
                    'extension'    => 'xml',
                    'directory'    => $group['directory']['id'],
                    'addDirectory' => true,
                    'hydrate'      => Query::HYDRATE_ARRAY
                ), true);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }
        $serieMeta = $this->getSerieParser()->parse($serieXml['directory']['path'] . '/' . $serieXml['name'] . '.' . $serieXml['extension']);
        $serieMeta['image'] = 'folder.jpg';

        $images = $this->getRepository('File\File')->findFullBy(
            array(
                'name'         => 'folder',
                'extension'    => 'jpg',
                'directory'    => $group['directory']['id'],
                'addDirectory' => true,
                'hydrate'      => Query::HYDRATE_ARRAY
            ));
        $image = count($images) ? $images[0] : null;

        $staff['actors'] = array_key_exists('actors', $serieMeta) ? $serieMeta['actors'] : array();
        $staff['directors'] = array_key_exists('directors', $serieMeta) ?
            is_array($serieMeta['directors']) ? $serieMeta['directors'] : array($serieMeta['directors']) :
            array();
        $staff['writers'] = array_key_exists('directors', $serieMeta) ?
            is_array($serieMeta['writers']) ? $serieMeta['writers'] : array($serieMeta['writers']) :
            array();


        $groupDocument = $this->getMongoRepository('Video\Group')->findOneBy(['groupRef' => (string)$group['id']]);

        if (!$groupDocument) {
            $groupDocument = new Group();
            $groupDocument->setGroupRef((string)$id);
        }
        /**
         * @var $groupDocument \MediaMine\CoreBundle\Document\Video\Group
         */
        $tunnelGroups = $groupDocument->getTunnels();

        /**
         * @var $tunnelGroup \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
         */
        $tunnelGroup = null;
        $newTunnel = true;
        foreach ($tunnelGroups as $tg) {
            /**
             * @var $tg \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
             */
            if ($this->getTunnelName() === $tg->getTunnel()) {
                $tunnelGroup = $tg;
                $groupDocument->removeTunnel($tunnelGroup);
                $newTunnel = false;
                break;
            }
        }
        if ($newTunnel) {
            $tunnelGroup = new TunnelGroup();
            $tunnelGroup->setTunnel($this->getTunnelName());
        }

        $images = [];
        if ($image) {
            $images[] = [$image['id'], $image['pathKey']];
        }
        $tunnelGroup->setData([
            'name'         => $serieMeta['name'],
            'originalName' => $serieMeta['originalName'],
            'summary'      => $serieMeta['summary'],
            'year'         => $serieMeta['productionYear'],
            'rating'       => $serieMeta['rating'],
            'genres'       => $serieMeta['genres'],
            'images'       => $images,
            'type'         => 'series',
            'staffs'       => $staff
        ]);

        $groupDocument->addTunnel($tunnelGroup);

        $this->getMongoManager()->persist($groupDocument);
        $this->getMongoManager()->flush();
        return true;
    }
}

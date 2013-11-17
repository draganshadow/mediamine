<?php
namespace MediaMine\Service;

use MediaMine\Entity\Video\Staff;
use MediaMine\Parser\MovieParser;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use MediaMine\Entity\File\Directory,
    MediaMine\Entity\File\File,
    MediaMine\Entity\Video\Group,
    MediaMine\Entity\Video\Season,
    MediaMine\Entity\Video\Video,
    MediaMine\Entity\Video\VideoFile,
    Doctrine\ORM\Query,
    MediaMine\Parser\SerieParser,
    MediaMine\Parser\EpisodeParser;

class XMLMetaSearchService extends AbstractService implements ServiceLocatorAwareInterface
{
    const SEP = ';';
    protected $statusList = array('new', 'modified');

    protected $rolesList;

    protected $typesList;

    protected $serieParser;

    protected $episodeParser;

    protected $movieParser;

    protected $mode;

    public function searchSeries() {
        $this->getRoles();
        $series = $this->getRepository('File\File')->findFullBy(null, 'series', 'xml', null);
        foreach ($series as $serie) {
            $serieMeta = $this->getSerieParser()->parse($serie->getPath());
            $group = $this->createGroup($serie);
            if ($group) {
            // Search Seasons
            $subDirectories = $this->getRepository('File\Directory')->findFullBy(array('parent' => $serie->directory, 'status' => $this->statusList));
            foreach ($subDirectories as $seasonDirectory) {
                $season = $this->createSeason($group, $seasonDirectory);
                $metadata = $this->getRepository('File\Directory')->findFullBy(array('parent' => $seasonDirectory, 'name' => 'metadata'));
                $videoFiles = $this->getRepository('File\File')->findFullBy($seasonDirectory, null, null, 'video');

                if (count($metadata)) {
                    $metadata = $metadata[0];
                    foreach ($videoFiles as $videoFile) {
                        $video = $this->createEpisode($season, $videoFile, $metadata, $serieMeta);
                    }
                    $this->markAdded($metadata);
                }
                $this->markAdded($seasonDirectory);
            }
            }
            $this->markAdded($serie);
            $this->markAdded($serie->directory);
        }
        $this->flush(true);
    }

    public function searchMovies() {
        $this->getTypes();
        $this->getRoles();
        $movieXmls = $this->getRepository('File\File')->findFullBy(null, 'movie', 'xml', null);
        foreach ($movieXmls as $movieXml) {
            $movieMeta = $this->getMovieParser()->parse($movieXml->getPath());
            $movieMeta['image'] = 'folder.jpg';
            $videoFiles = $this->getRepository('File\File')->findFullBy($movieXml->directory, null, null, 'video');
            if (count($videoFiles)) {
                $videoFile = $videoFiles[0];
                $video = $this->createVideo($movieMeta, $videoFile, $movieXml->directory, null, $this->typesList['movie']);
                $this->markAdded($videoFile);
            }
            $this->markAdded($movieXml);
        }
        $this->flush(true);
    }

    protected function createGroup($serie)
    {
        $serieMeta = $this->getSerieParser()->parse($serie->getPath());
        $group = null;
        $action = 'add';
        if (!empty($serieMeta['name'])) {
            if ($serie->status == 'new') {
                $image = $this->getRepository('File\File')->findFullBy($serie->directory, 'folder', 'jpg');
                $image = (count($image)) ? $image[0] : null;
                $group = $this->getRepository('Video\Group')->createGroup($serieMeta['name'], $serieMeta['summary'], $image);
                $this->flush();
            } elseif ($serie->status == 'modified') {
                $group = $this->getRepository('Video\Group')->findFullBy($serieMeta['name']);
                $group = $group[0];
                $action = 'update';
            } else {
                $group = $this->getRepository('Video\Group')->findFullBy($serieMeta['name']);
                $group = $group[0];
                $action = 'read';
            }
        }
        $this->export(array($action, $serie->directory->name, $serieMeta['name'], $group ? $group->name : 'null'));
        return $group;
    }

    protected function createSeason($group, $seasonDirectory)
    {
        $image = $this->getRepository('File\File')->findFullBy($seasonDirectory, 'folder', 'jpg');
        $image = (count($image)) ? $image[0] : null;
        $matches = array();
        preg_match('/[0-9]+/', $seasonDirectory->name, $matches);
        $number = $matches[0];
        //echo $serie->directory->name, ' - ', $seasonDirectory->name, PHP_EOL;
        $season = $this->getRepository('Video\Season')->createSeason($group, $number, $seasonDirectory->name, $group->summary, $image);
        $this->flush();

        return $season;
    }

    protected function createEpisode($season, $videoFile, $metadata, $serieMeta)
    {
        $video = null;
        $videoMetaFile = $this->getRepository('File\File')->findFullBy($metadata, $videoFile->name, null, null, $this->statusList);
        if (count($videoMetaFile)) {
            $videoMetaFile = $videoMetaFile[0];
            $videoMeta = $this->getEpisodeParser()->parse($videoMetaFile->getPath());
            $videoMeta['actors'] = $serieMeta['actors'];
            $video = $this->createVideo($videoMeta, $videoFile, $metadata, $season, $this->typesList['movie']);
            $this->markAdded($videoMetaFile);
        }
        return $video;
    }

    protected function createVideo($videoMeta, $videoFile, $imageFolder, $season = null, $type){
        $video = null;
        $videos = $this->getRepository('Video\Video')->findFullBy($videoFile);
        if (!empty($videoMeta['name'])) {
            $image = null;
            if (!empty($videoMeta['image'])) {
                $images = $this->getRepository('File\File')->findFullBy($imageFolder, substr($videoMeta['image'], 0, strrpos($videoMeta['image'], '.')));
                $image = (count($images)) ? $images[0] : null;
            }

            if (count($videos)) {
                $video = $videos[0];
                $video->name = $videoMeta['name'];
                $video->summary = $videoMeta['summary'];
                $video->number = $videoMeta['number'];
                $this->getEntityManager()->persist($video);
                $this->flush();
            } else {
                $number = array_key_exists('number', $videoMeta) ? $videoMeta['number'] : null;
                $summary = array_key_exists('summary', $videoMeta) ? $videoMeta['summary'] : null;
                $video = $this->getRepository('Video\Video')->createVideo($videoMeta['name'], $summary, $image, $season, $number,$type);
                $this->flush();
                $this->getRepository('Video\VideoFile')->createVideoFile($video, $videoFile);
                $this->flush();
            }
        }
        $this->markAdded($videoFile);

        if ($video) {
            //Create staff
            if (array_key_exists('actors', $videoMeta)) {
                $this->createStaff($videoMeta['actors'], $video, 'actor');
            }
            //Create staff
            if (array_key_exists('actors', $videoMeta)) {
                $this->createStaff($videoMeta['guests'], $video, 'guest');
            }
            //Create staff
            if (array_key_exists('actors', $videoMeta)) {
                $this->createStaff($videoMeta['directors'], $video, 'director');
            }
            //Create staff
            if (array_key_exists('actors', $videoMeta)) {
                $this->createStaff($videoMeta['writers'], $video, 'writer');
            }
        }

        return $video;
    }

    /**
     *
     */
    protected function createStaff($names, $video, $role)
    {
        foreach($names as $name) {
            $persons = $this->getRepository('Common\Person')->findFullBy($name);
            if (count($persons)) {
                $person = $persons[0];
            } else {
                $person = $this->getRepository('Common\Person')->createPerson($name);
                $this->flush();
            }
            $staff = $this->getRepository('Video\Staff')->createStaff($video, $person, null, $this->rolesList[$role]);
        }
    }

    /**
     * @param $entity
     */
    protected function markAdded($entity) {
        if (!$this->isDebug()) {
            $entity->status = 'added';
            $this->getEntityManager()->persist($entity);
            $this->flush();
        }
    }

    protected function getRoles() {
        $roles = $this->getRepository('Video\StaffRole')->findAll();
        foreach($roles as $role) {
            $this->rolesList[$role->name] = $role;
        }
    }

    protected function getTypes() {
        $types = $this->getRepository('Video\Type')->findAll();
        foreach($types as $type) {
            $this->typesList[$type->name] = $type;
        }
    }

    protected function getSerieParser() {
        if (null === $this->serieParser) {
            $this->serieParser = new SerieParser();
        }
        return $this->serieParser;
    }

    protected function getEpisodeParser() {
        if (null === $this->episodeParser) {
            $this->episodeParser = new EpisodeParser();
        }
        return $this->episodeParser;
    }

    protected function getMovieParser() {
        if (null === $this->movieParser) {
            $this->movieParser = new MovieParser();
        }
        return $this->movieParser;
    }

    protected function getMode() {
        if (null === $this->mode) {
            $this->mode = 'DEBUG';
        }
        return $this->mode;
    }

    protected function isDebug() {
        return 'DEBUG' === $this->getMode();
    }

    protected function export($values) {
        echo implode(self::SEP, $values) . PHP_EOL;
    }
}

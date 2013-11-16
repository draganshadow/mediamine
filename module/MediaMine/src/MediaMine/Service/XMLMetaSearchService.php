<?php
namespace MediaMine\Service;

use MediaMine\Entity\Video\Staff;
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

    protected $statusList = array('new', 'modified');

    protected $rolesList;

    public function searchSeries() {
        $this->getRoles();
        $series = $this->getRepository('File\File')->findFullBy(null, 'series', 'xml', null);
        $serieParser = new SerieParser();
        $episodeParser = new EpisodeParser();
        foreach ($series as $serie) {
            $serieMeta = $serieParser->parse($serie->getPath());
            if (!empty($serieMeta['name'])) {
                if ($serie->status == 'new') {
                    $image = $this->getRepository('File\File')->findFullBy($serie->directory, 'folder', 'jpg');
                    $image = (count($image)) ? $image[0] : null;
                    $group = $this->getRepository('Video\Group')->createGroup($serieMeta['name'], $serieMeta['summary'], $image);
                    $this->flush();
                } elseif ($serie->status == 'modified') {
                    $group = $this->getRepository('Video\Group')->findFullBy($serieMeta['name']);
                    $group = $group[0];
                } else {
                    $group = $this->getRepository('Video\Group')->findFullBy($serieMeta['name']);
                    $group = $group[0];
                }
            }

            // Search Seasons
            $subDirectories = $this->getRepository('File\Directory')->findFullBy($serie->directory, null, null, $this->statusList);
            foreach ($subDirectories as $seasonDirectory) {
                $image = $this->getRepository('File\File')->findFullBy($seasonDirectory, 'folder', 'jpg');
                $image = (count($image)) ? $image[0] : null;
                $matches = array();
                preg_match('/[0-9]+/', $seasonDirectory->name, $matches);
                $number = $matches[0];
                echo $serie->directory->name, ' - ', $seasonDirectory->name, PHP_EOL;
                $season = $this->getRepository('Video\Season')->createSeason($group, $number, $seasonDirectory->name, $serieMeta['summary'], $image);
                $this->flush();
                $metadata = $this->getRepository('File\Directory')->findFullBy($seasonDirectory, 'metadata');
                $videoFiles = $this->getRepository('File\File')->findFullBy($seasonDirectory, null, null, 'video');

                if (count($metadata)) {
                    $metadata = $metadata[0];
                    foreach ($videoFiles as $videoFile) {
                        echo $videoFile->name, PHP_EOL;
                        $videoMetaFile = $this->getRepository('File\File')->findFullBy($metadata, $videoFile->name, null, null, $this->statusList);
                        $videos = $this->getRepository('Video\Video')->findFullBy($videoFile);
                        if (count($videoMetaFile)) {
                            $videoMetaFile = $videoMetaFile[0];
                            $videoMeta = $episodeParser->parse($videoMetaFile->getPath());
                            if (!empty($videoMeta['name'])) {
                                $image = null;
                                if (!empty($videoMeta['image'])) {
                                    $images = $this->getRepository('File\File')->findFullBy($metadata, substr($videoMeta['image'], 0, strrpos($videoMeta['image'], '.')));
                                    $image = (count($images)) ? $images[0] : null;
                                }

                                if (count($videos)) {
                                    $video = $videos[0];
                                    $video->name = $videoMeta['name'];
                                    $video->summary = $videoMeta['summary'];
                                    $video->number = $videoMeta['number'];
                                    $this->getEntityManager()->persist($video);
                                    $this->flush();
                                    echo '++++' . $video->name, PHP_EOL;
                                } else {
                                    $video = $this->getRepository('Video\Video')->createVideo($videoMeta['name'], $videoMeta['summary'], $image, $season, $videoMeta['number']);
                                    $this->flush();
                                    $this->getRepository('Video\VideoFile')->createVideoFile($video, $videoFile);
                                    $this->flush();
                                }
                                //Create staff
                                $this->createStaff($serieMeta['actors'], $video, 'actor');
                            }
                            $this->markAdded($videoMetaFile);
                            $this->markAdded($videoFile);
                        }
                        //Create staff
                        $this->createStaff($videoMeta['guests'], $video, 'guest');
                        //Create staff
                        $this->createStaff($videoMeta['directors'], $video, 'director');
                        //Create staff
                        $this->createStaff($videoMeta['writers'], $video, 'writer');
                    }
                    $this->markAdded($metadata);
                }
                $this->markAdded($seasonDirectory);
            }
            $this->markAdded($serie);
            $this->markAdded($serie->directory);
        }
        $this->flush(true);
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
        $entity->status = 'added';
        $this->getEntityManager()->persist($entity);
        $this->flush();
    }

    protected function getRoles() {
        $roles = $this->getRepository('Video\StaffRole')->findAll();
        foreach($roles as $role) {
            $this->rolesList[$role->name] = $role;
        }
    }
}

<?php
namespace MediaMine\Service\Tunnel;

use MediaMine\Service\AbstractService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

abstract class AbstractTunnel extends AbstractService implements ServiceLocatorAwareInterface
{
    const SEP = ';';
    protected $statusList = array('new', 'modified');
    protected $rolesList;

    protected $typesList;
    protected $mode;

    protected $es;

    protected $genres = array();
    /**
     * Must return array of handled entities and fields
     * @return array
     */
    abstract function getAbilities();

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
                $video->originalName = $videoMeta['originalName'];
                $video->year = $videoMeta['productionYear'];

                $this->getEntityManager()->persist($video);
                $this->index($video);
                $this->flush();
            } else {
                $number = array_key_exists('number', $videoMeta) ? $videoMeta['number'] : null;
                $summary = array_key_exists('summary', $videoMeta) ? $videoMeta['summary'] : null;
                $originalName = array_key_exists('originalName', $videoMeta) ? $videoMeta['originalName'] : null;
                $productionYear = array_key_exists('productionYear', $videoMeta) ? $videoMeta['productionYear'] : null;
                $video = $this->getRepository('Video\Video')->createVideo($videoMeta['name'], $summary, $image, $season, $number,$type, $originalName, $productionYear);
                $this->index($video);
                $this->flush();
                $this->getRepository('Video\VideoFile')->createVideoFile($video, $videoFile);
                $this->flush();
            }

            if (!empty($videoMeta['genres'])) {
                $genres = $this->createGenres($videoMeta['genres']);
                $video->addAllGenre($genres);
                $this->getEntityManager()->persist($video);
            }
        }
        $this->markAdded($videoFile);

        if ($video) {
            //Create staff
            //TODO handle update
            if (array_key_exists('persons', $videoMeta)) {
                $this->createStaff($videoMeta['persons'], $video);
            }
        }

        return $video;
    }

    /**
     * @param \MediaMine\Entity\Video\Video $video
     */
    protected function index($video)
    {
        $doc = new \Elastica\Document(
            $video->id,
            array(
                'id' => $video->id,
                'year' => $video->year,
                'title' => $this->replaceAccents($video->name) . ' ' . $this->replaceAccents($video->originalName),
                'description' => $this->replaceAccents($video->summary)
            )
        );
        $this->getEs()->getIndex('mediamine')->getType('video')->addDocument($doc);
        $this->getEs()->getIndex('mediamine')->refresh();
    }

    /**
     *
     */
    protected function createStaff($staffs, $video)
    {
        foreach($staffs as $staff) {
            $persons = $this->getRepository('Common\Person')->findFullBy($staff['name']);
            if (count($persons)) {
                $person = $persons[0];
            } else {
                $person = $this->getRepository('Common\Person')->createPerson($staff['name']);
                $this->flush();
            }
            $character = $this->getRepository('Video\Character')->createCharacter($video, $staff['role']);
            $staff = $this->getRepository('Video\Staff')->createStaff($video, $person, $character, $this->rolesList[strtolower($staff['type'])]);
        }
    }

    /**
     *
     */
    protected function createGenres($genreNames)
    {
        $videoGenres = array();
        foreach($genreNames as $genreName) {
            if (array_key_exists(strtolower($genreName), $this->genres)) {
                $genre = $this->genres[strtolower($genreName)];
            } else {
                $genre = $this->getRepository('Video\Genre')->createGenre($genreName);
                $this->genres[strtolower($genreName)] = $genre;
            }
            $videoGenres[] = $genre;
        }
        return $videoGenres;
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

    protected function getEs() {
        if (null === $this->es) {
            $this->es = $this->getServiceLocator()->get('elasticsearch');
        }
        return $this->es;
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

    protected function replaceAccents($string)
    {
        return str_replace(
            array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),
            array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
            $string);
    }
}

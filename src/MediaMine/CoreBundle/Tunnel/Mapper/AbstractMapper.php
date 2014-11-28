<?php

namespace MediaMine\CoreBundle\Tunnel\Mapper;


use Doctrine\ORM\Query;
use Gedmo\Sluggable\Util\Urlizer;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Entity\Video\Genre;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Service\SettingService;
use MediaMine\CoreBundle\Service\TaskService;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use MediaMine\CoreBundle\Shared\MongoEntitityManagerAware;

class AbstractMapper {

    use MongoEntitityManagerAware;
    use EntitityManagerAware;
    use LoggerAware;

    const CACHE_TIMEOUT = 86400;

    protected $countries = array();

    /**
     * @Inject("mediamine.service.task")
     * @var TaskService
     */
    public $taskService;

    /**
     * @Inject("mediamine.service.setting")
     * @var SettingService
     */
    public $settingService;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    protected $urlizer;

    public function __construct() {
        $this->urlizer = new Urlizer();
    }

    public function clear() {
        unset($this->genres);
        $this->genres = [];
        unset($this->countries);
        $this->countries = [];
    }

    protected function transliterate($text) {
        return $this->urlizer->transliterate($text);
    }

    protected function loadGenres()
    {
        $this->redis->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", ['mediamine.mapper.*']);
        $allKeys = $this->redis->keys('*');
        var_dump($allKeys);
        $genres = $this->getRepository('Video\Genre')->findFullBy(['hydrate' => Query::HYDRATE_ARRAY]);
        foreach ($genres as $genre) {
            $this->setGenre($genre['name'], $genre['id']);
        }

    }

    protected function setGenre($name, $id) {
        $this->redis->set('mediamine.mapper.' . $this->transliterate($name), $id, self::CACHE_TIMEOUT);
    }

    protected function getGenre($name) {
        return $this->redis->get('mediamine.mapper.' . $this->transliterate($name));
    }

    protected function loadCountries()
    {
        $countries = $this->getRepository('Common\Country')->findAll();
        foreach ($countries as $country) {
            $this->countries[strtolower($country->name)] = $country;
        }
    }

    protected function getCountry($name)
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->countries) ? $this->countries[$name] : null;
    }


    protected function getCreateGenres($genreNames)
    {
        $videoGenres = [];
        if ($genreNames) {
            foreach ($genreNames as $genreName) {
                $genreName = $this->transliterate($genreName);
                $genreId = (int) $this->getGenre($genreName);
                if ($genreId) {
                    $genre = $this->getEntityManager()->getReference('MediaMine\CoreBundle\Entity\Video\Genre', $genreId);
                } else {
                    /**
                     * @var $genre Genre
                     */
                    $genre = $this->getRepository('Video\Genre')->create(['name' => $genreName]);
                    $this->getEntityManager()->flush();
                    $this->setGenre($genreName, $genre->getId());
                }
                $videoGenres[] = $genre;
            }
        }
        return $videoGenres;
    }


    protected function getCreateGroup($groupName, $directory = null)
    {
        $group = $this->getRepository('Video\Group')->findFullBy(array('name' => $groupName));
        if (count($group)) {
            $group = $group[0];
        } else {
            $group = $this->getRepository('Video\Group')->create(array(
                'name'      => $groupName,
                'directory' => $directory,
            ));
            $this->getEntityManager()->persist($group);
            $this->getEntityManager()->flush();
        }

        return $group;
    }

    protected function getCreateSeason(Group $group, $seasonNumber, $directory = null)
    {
        $season = $this->getRepository('Video\Season')->findFullBy(array(
            'group'  => $group->id,
            'number' => $seasonNumber
        ));
        if (count($season)) {
            $season = $season[0];
        } else {
            $season = $this->getRepository('Video\Season')->create(array(
                'group'     => $group,
                'number'    => $seasonNumber,
                'name'      => 'Season ' . $seasonNumber,
                'summary'   => $group->summary,
                'directory' => $directory
            ));
            if ($directory) {
                $files = $this->getRepository('File\File')->findFullBy(
                    array(
                        'name'      => 'folder',
                        'extension' => 'jpg',
                        'directory' => $directory
                    ));
                if (count($files)) {
                    $season->addImage($files[0]);
                }
            }
            $this->getEntityManager()->persist($season);
            $this->getEntityManager()->flush();
        }

        return $season;
    }

    protected function getCreatePersons($personNames)
    {
        $persons = $this->getRepository('Common\Person')->findFullBy(array('name' => $personNames));
        $result = array();
        foreach ($persons as $p) {
            $result[$p->name] = $p;
        }
        foreach ($personNames as $pn) {
            if (!array_key_exists($pn, $result)) {
                $person = $this->getRepository('Common\Person')->create(array('name' => $pn));
                $this->getEntityManager()->flush();
                $result[$person->name] = $person;
            }
        }
        return $result;
    }

    protected function getCreateCharacters($video, $characterNames)
    {
        $result = [];
        if (count($characterNames)) {
            $characters = $this->getRepository('Video\Character')->findFullBy(array('video' => $video, 'name' => $characterNames));
            foreach ($characters as $c) {
                $result[$c->name] = $c;
            }
            foreach ($characterNames as $cn) {
                if ($cn && !array_key_exists($cn, $result)) {
                    $character = $this->getRepository('Video\Character')->create(array('video' => $video, 'name' => $cn));
                    $result[$character->name] = $character;
                }
            }
            $this->getEntityManager()->flush();
        }
        return $result;
    }

    protected function getCreatePerson($personName)
    {
        $person = $this->getRepository('Common\Person')->findFullBy(array('name' => $personName));
        if (count($person)) {
            $person = $person[0];
        } else {
            $person = $this->getRepository('Common\Person')->create(array('name' => $personName));
        }
        $this->getEntityManager()->flush();
        return $person;
    }

    protected function getCreateCharacter($video, $characterName)
    {
        $character = $this->getRepository('Video\Character')->findFullBy(array('video' => $video, 'name' => $characterName));
        if (count($character)) {
            $character = $character[0];
        } else {
            $character = $this->getRepository('Video\Character')->create(array('video' => $video, 'name' => $characterName));
            $this->getEntityManager()->flush();
        }
        return $character;
    }

    protected function createStaff($video, $person, $role, $character = null)
    {
        $staff = $this->getRepository('Video\Staff')->create(array(
            'video'     => $video,
            'person'    => $person,
            'character' => $character,
            'role'      => $role
        ));
    }

    protected function clearStaff($video)
    {
        $staffs = $this->getRepository('Video\Staff')->findFullBy(array('video' => $video));
        foreach ($staffs as $staff) {
            $this->getEntityManager()->remove($staff);
        }
    }

    /**
     * @return SettingService
     */
    public function getSettingService()
    {
        return $this->settingService;
    }

    /**
     * @param SettingService $settingService
     */
    public function setSettingService($settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @return TaskService
     */
    public function getTaskService()
    {
        return $this->taskService;
    }

    /**
     * @param TaskService $taskService
     */
    public function setTaskService($taskService)
    {
        $this->taskService = $taskService;
    }
}
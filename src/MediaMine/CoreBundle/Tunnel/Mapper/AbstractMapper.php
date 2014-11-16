<?php

namespace MediaMine\CoreBundle\Tunnel\Mapper;


use JMS\DiExtraBundle\Annotation\Inject;
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

    protected $genres = array();
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

    public function clear() {
        unset($this->genres);
        $this->genres = [];
        unset($this->countries);
        $this->countries = [];
    }

    protected function loadGenres()
    {
        $genres = $this->getRepository('Video\Genre')->findAll();
        foreach ($genres as $genre) {
            $this->genres[strtolower($genre->name)] = $genre;
        }
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
        $videoGenres = array();
        if ($genreNames) {
            foreach ($genreNames as $genreName) {
                $genreName = strtolower($genreName);
                if (array_key_exists($genreName, $this->genres)) {
                    $genre = $this->genres[$genreName];
                } else {
                    $genre = $this->getRepository('Video\Genre')->create(array('name' => strtolower($genreName)));
                    $this->getEntityManager()->flush();
                    $this->genres[strtolower($genreName)] = $genre;
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
        $characters = $this->getRepository('Video\Character')->findFullBy(array('video' => $video, 'name' => $characterNames));
        $result = array();
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
        return $result;
    }

    protected function getGenre($name)
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->genres) ? $this->genres[$name] : null;
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
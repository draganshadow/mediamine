<?php

namespace MediaMine\CoreBundle\Tunnel\Mapper;


use Doctrine\ORM\Query;
use Gedmo\Sluggable\Util\Urlizer;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Entity\Video\Season;
use MediaMine\CoreBundle\Service\SettingService;
use MediaMine\CoreBundle\Service\TaskService;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use MediaMine\CoreBundle\Shared\MongoEntitityManagerAware;

class AbstractMapper
{

    use MongoEntitityManagerAware;
    use EntitityManagerAware;
    use LoggerAware;

    const CACHE_CONTEXT = 'MAPPER';
    const CACHE_TIMEOUT = 86400;
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
    protected $countries = array();
    protected $urlizer;

    public function __construct()
    {
        $this->urlizer = new Urlizer();
    }

    public function clear()
    {
        $this->getRepository('Video\Genre')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Common\Country')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Video\Season')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Video\Group')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Common\Person')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Video\Character')->clearCache(false, self::CACHE_CONTEXT);
        $this->getRepository('Video\Staff')->clearCache(false, self::CACHE_CONTEXT);
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

    protected function transliterate($text)
    {
        return $this->urlizer->transliterate($text);
    }

    protected function loadGenres()
    {
        $genres = $this->getRepository('Video\Genre')->findFullBy(['hydrate' => Query::HYDRATE_ARRAY]);
        $this->getRepository('Video\Genre')->cacheAll($genres, ['name'], self::CACHE_CONTEXT);
    }

    protected function getCreateGenre($genreName)
    {
        $genre = $this->getRepository('Video\Genre')
            ->getCachedOrCreate(['name' => $genreName], ['name'], self::CACHE_CONTEXT);
        return $genre;
    }

    protected function getCreateGenres($genreNames)
    {
        $result = [];
        foreach ($genreNames as $genreName) {
            $result[$genreName] = $this->getCreateGenre($genreName);
        }
        return $result;
    }

    protected function loadCountries()
    {
        $countries = $this->getRepository('Common\Country')->findFullBy(['hydrate' => Query::HYDRATE_ARRAY]);
        $this->getRepository('Common\Country')->cacheAll($countries, ['name'], self::CACHE_CONTEXT);
    }

    protected function getCreateCountry($countryName)
    {
        $genre = $this->getRepository('Common\Country')
            ->getCachedOrCreate(['name' => $countryName, 'language' => $countryName], ['name'], self::CACHE_CONTEXT);
        return $genre;
    }

    protected function getCreateCountries($countryNames)
    {
        $result = [];
        foreach ($countryNames as $countryName) {
            $result[$countryName] = $this->getCreateCountry($countryName);
        }
        return $result;
    }

    protected function loadGroups()
    {
        $groups = $this->getRepository('Video\Group')->findFullBy(['hydrate' => Query::HYDRATE_ARRAY]);
        $this->getRepository('Video\Group')->cacheAll($groups, ['name'], self::CACHE_CONTEXT);
    }

    protected function loadSeasons()
    {
        $seasons = $this->getRepository('Video\Season')->findFullBy(['addGroup' => true, 'hydrate' => Query::HYDRATE_ARRAY]);
        $this->getRepository('Video\Season')->cacheAll($seasons, ['group', 'number'], self::CACHE_CONTEXT);
    }

    protected function getCreateGroup($groupName, $directory = null)
    {
        /**
         * @var $group Group
         */
        $group = $this->getRepository('Video\Group')->getCachedOrCreate([
            'name'      => $groupName,
            'directory' => $directory], ['name'], self::CACHE_CONTEXT, $cached);
        if ($directory && !$cached) {
            $files = $this->getRepository('File\File')->findFullBy(
                array(
                    'name'      => 'folder',
                    'extension' => 'jpg',
                    'directory' => $directory
                ));
            if (count($files)) {
                $group->addImage($files[0]);
                $this->getEntityManager()->persist($group);
                $this->getEntityManager()->flush();
            }
        }
        return $group;
    }

    protected function getCreateSeason(Group $group, $seasonNumber, $directory = null)
    {
        $values = [
            'group'     => $group,
            'number'    => (int) $seasonNumber,
            'name'      => 'Season ' . $seasonNumber,
            'summary'   => $group->summary,
            'directory' => $directory
        ];
        /**
         * @var $season Season
         */
        $season = $this->getRepository('Video\Season')->getCachedOrCreate($values, ['group', 'number'], self::CACHE_CONTEXT, $cached);
        if ($directory && !$cached) {
            $files = $this->getRepository('File\File')->findFullBy(
                array(
                    'name'      => 'folder',
                    'extension' => 'jpg',
                    'directory' => $directory
                ));
            if (count($files)) {
                $season->addImage($files[0]);
                $this->getEntityManager()->persist($season);
                $this->getEntityManager()->flush();
            }
        }
        return $season;
    }

    protected function getCreatePerson($personName)
    {
        $person = $this->getRepository('Common\Person')->getCached(['name' => $personName], ['name'], self::CACHE_CONTEXT);
        if (false === $person) {
            $person = $this->getRepository('Common\Person')->getOrCreate(['name' => $personName], ['name'], self::CACHE_CONTEXT);
        }
        return $person;
    }

    protected function getCreatePersons($personNames)
    {
        $result = [];
        foreach ($personNames as $personName) {
            $result[$personName] = $this->getCreatePerson($personName);
        }
        return $result;
    }

    protected function getCreateCharacter($video, $characterName)
    {
        $character = $this->getRepository('Video\Character')
            ->getCachedOrCreate(['video' => $video, 'name' => $characterName], ['video', 'name'], self::CACHE_CONTEXT);
        return $character;
    }

    protected function getCreateCharacters($video, $characterNames)
    {
        $result = [];
        foreach ($characterNames as $characterName) {
            $result[$characterName] = $this->getCreateCharacter($video, $characterName);
        }
        return $result;
    }

    protected function getCreateStaff($video, $person, $role, $character = null)
    {
        $values = [
            'video'     => $video,
            'person'    => $person,
            'role'      => $role
        ];
        if ($character) {
            $values['character'] = $character;
        }
        $staff = $this->getRepository('Video\Staff')->getCachedOrCreate($values
            , ['video', 'person', 'character', 'role'], self::CACHE_CONTEXT);

        return $staff;
    }

    protected function clearStaff($video)
    {
        $staffs = $this->getRepository('Video\Staff')->findFullBy(['video' => $video]);
        foreach ($staffs as $staff) {
            $this->getEntityManager()->remove($staff);
        }
    }
}
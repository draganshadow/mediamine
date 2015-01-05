<?php
namespace MediaMine\CoreBundle\Tunnel\Mapper;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Entity\Video\StaffRole;
use MediaMine\CoreBundle\Entity\Video\Video;


/**
 * @Service("mediamine.mapper.group")
 * @Tag("monolog.logger", attributes = {"channel" = "GroupMapper"})
 */
class GroupMapper extends AbstractMapper
{

    public function mapAllGroupData(Job $job)
    {
        $this->clear();

        $this->loadGenres();
        $params = $job->getParams();
        $nbTasks = 0;

        if (array_key_exists('groups', $params)) {
            foreach ($params['groups'] as $groupId) {
                $this->taskService->createTask($job, 'mediamine.mapper.group', 'mapGroupData', ['id' => $groupId]);
                $nbTasks++;
                $this->taskService->createTask($job, 'mediamine.mapper.group', 'mapGroupDataEpisode', ['id' => $groupId]);
                $nbTasks++;
            }
        } else {
            $params = [
                'hydrate' => Query::HYDRATE_ARRAY
            ];
            $iterableResult = $this->getRepository('Video\Group')->findFullBy($params, 2, false);
            foreach ($iterableResult as $row) {
                $group = $row[0];
                $this->taskService->createTask($job, 'mediamine.mapper.group', 'mapGroupData', ['id' => $group['id']]);
                $nbTasks++;
                $this->taskService->createTask($job, 'mediamine.mapper.group', 'mapGroupDataEpisode', ['id' => $group['id']]);
                $nbTasks++;
            }
        }
        return $nbTasks;
    }

    public function mapGroupData($param)
    {
        $id = $param['id'];
        $t1 = microtime(true);
        $group = $this->getRepository('Video\Group')->findFullBy(
            array(
                'id'           => $id,
                'addDirectory' => true
            ), true);

        /**
         * @var $group \MediaMine\CoreBundle\Entity\Video\Group
         */
        $name = $group->name;
        $this->loadCountries();

        $settings = $this->getSettingService()->getSetting('tunnel', 'group');

        /**
         * @var $groupDocument \MediaMine\CoreBundle\Document\Video\Group
         */
        $groupDocument = $this->getMongoRepository('Video\Group')->findOneBy(['groupRef' => (string)$group->id]);


        if ($groupDocument) {
            $tunnelGroups = $groupDocument->getTunnels();
            if (count($tunnelGroups)) {
                $tgs = array();
                foreach ($tunnelGroups as $tg) {
                    /**
                     * @var $tg \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
                     */
                    $tgs[$tg->getTunnel()] = $tg;
                }
                $override = true;
                foreach ($settings as $t) {
                    /**
                     * @var $tg \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
                     */
                    $tg = $tgs[$t];
                    $this->applyGroupTunnelData($tg->getData(), $group, $override);
                    $override = false;
                }
            }
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
            $t2 = microtime(true);
            $t = $t2 - $t1;
            $this->getLogger()->info('map group data : ' . $name . '->' . $t . ' ms');
        }
        $this->clear();
    }

    protected function applyGroupTunnelData($data, Group $group, $override = false)
    {
        $images = array();
        if (array_key_exists('images', $data)) {
            foreach ($data['images'] as $image) {
                $images[] = $this->getEntityManager()->getReference('\MediaMine\CoreBundle\Entity\File\File', $image[0]);
            }
        }

        $tunnelData = array(
            'name'         => array_key_exists('name', $data) ? $data['name'] : null,
            'originalName' => array_key_exists('originalName', $data) ? $data['originalName'] : null,
            'summary'      => array_key_exists('summary', $data) ? $data['summary'] : null,
            'images'       => $images,
            'type'         => array_key_exists('type', $data) ? $data['type'] : null,
            'genres'       => array_key_exists('genres', $data) ? $this->getCreateGenres($data['genres']) : null,
        );

        $personNames = array_unique(
            array_filter(array_merge(
                array_key_exists('directors', $data['staffs']) ? $data['staffs']['directors'] : [],
                array_key_exists('writers', $data['staffs']) ? $data['staffs']['writers'] : [],
                array_key_exists('actors', $data['staffs']) ? $data['staffs']['actors'] : []
                ))
        );
        $persons = $this->getCreatePersons($personNames);

        if ($override) {
            $group = $this->getRepository('Video\Group')->exchangeArrayNoEmpty($tunnelData, $group);
        }
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();
    }

    public function mapGroupDataEpisode($param)
    {
        $id = $param['id'];
        $t1 = microtime(true);
        $group = $this->getRepository('Video\Group')->findFullBy(
            array(
                'id'           => $id,
                'addDirectory' => true
            ), true);
        $episodes = $this->getRepository('Video\Video')->findFullBy(
            array(
                'group' => $id,
            ));

        /**
         * @var $group \MediaMine\CoreBundle\Entity\Video\Group
         */
        $name = $group->name;
        $this->loadCountries();

        $settings = $this->getSettingService()->getSetting('tunnel', 'group');

        /**
         * @var $groupDocument \MediaMine\CoreBundle\Document\Video\Group
         */
        $groupDocument = $this->getMongoRepository('Video\Group')->findOneBy(['groupRef' => (string)$group->id]);


        if ($groupDocument) {
            $tunnelGroups = $groupDocument->getTunnels();
            if (count($tunnelGroups)) {
                $tgs = array();
                foreach ($tunnelGroups as $tg) {
                    /**
                     * @var $tg \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
                     */
                    $tgs[$tg->getTunnel()] = $tg;
                }
                $override = true;
                foreach ($settings as $t) {
                    /**
                     * @var $tg \MediaMine\CoreBundle\Document\Tunnel\TunnelGroup
                     */
                    $tg = $tgs[$t];
                    foreach ($episodes as $episode) {
                        $this->applyGroupTunnelDataEpisode($tg->getData(), $episode, $override);
                    }
                    $override = false;
                }
            }
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
            $t2 = microtime(true);
            $t = $t2 - $t1;
            $this->getLogger()->info('map group data : ' . $name . '->' . $t . ' ms');
        }
        $this->clear();
    }

    protected function applyGroupTunnelDataEpisode($data, Video $video, $override = false)
    {
        $tunnelData = array(
            'summary'      => array_key_exists('summary', $data) ? $data['summary'] : null
        );

        $genres = array_key_exists('genres', $data) ? $this->getCreateGenres($data['genres']) : [];
        foreach ($genres as $genre) {
            $video->addGenre($genre);
        }

        $personNames = array_unique(
            array_filter(array_merge(
                array_key_exists('directors', $data['staffs']) ? $data['staffs']['directors'] : [],
                array_key_exists('writers', $data['staffs']) ? $data['staffs']['writers'] : [],
                array_key_exists('actors', $data['staffs']) ? $data['staffs']['actors'] : []
            ))
        );
        $persons = $this->getCreatePersons($personNames);
        $staffs = $video->getStaffByRole();

        if (is_array($video->staffs)) {
            foreach ($video->staffs as $staff) {
                if (!array_key_exists($staff->person->name, $persons)) {
                    $this->getEntityManager()->remove($staff);
                }
            }
        }

        if (array_key_exists('directors', $data['staffs'])) {
            foreach ($data['staffs']['directors'] as $name) {
                if (array_key_exists($name, $persons) && (!array_key_exists('director', $staffs) || !array_key_exists($name, $staffs['director']))) {
                    $this->getCreateStaff($video, $persons[$name], StaffRole::DIRECTOR);
                }
            }
        }
        if (array_key_exists('writers', $data['staffs'])) {
            foreach ($data['staffs']['writers'] as $name) {
                if (array_key_exists($name, $persons) && (!array_key_exists('writer', $staffs) || !array_key_exists($name, $staffs['writer']))) {
                    $this->getCreateStaff($video, $persons[$name], StaffRole::WRITER);
                }
            }
        }

        if (array_key_exists('actors', $data['staffs'])) {
            foreach ($data['staffs']['actors'] as $name) {
                if (array_key_exists($name, $persons) && (!array_key_exists('actor', $staffs) || !array_key_exists($name, $staffs['actor']))
                ) {
                    $this->getCreateStaff($video, $persons[$name], StaffRole::ACTOR);
                }
            }
        }

        $video = $this->getRepository('Video\Video')->exchangeArrayNoEmpty($tunnelData, $video);

        $this->getEntityManager()->persist($video);
        $this->getEntityManager()->flush();
    }
} 
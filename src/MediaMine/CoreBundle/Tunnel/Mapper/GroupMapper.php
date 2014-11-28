<?php
namespace MediaMine\CoreBundle\Tunnel\Mapper;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\Video\Group;
use MediaMine\CoreBundle\Entity\System\Job;


/**
 * @Service("mediamine.mapper.group")
 * @Tag("monolog.logger", attributes = {"channel" = "GroupMapper"})
 */
class GroupMapper extends AbstractMapper {

    public function mapAllGroupData(Job $job)
    {
        $this->loadGenres();
        $params = [
            'hydrate' => Query::HYDRATE_ARRAY
        ];
        $iterableResult = $this->getRepository('Video\Group')->findFullBy($params, 2, false);
        $nbTasks = 0;
        foreach ($iterableResult as $row) {
            $group = $row[0];
            $this->taskService->createTask($job, 'mediamine.mapper.group', 'mapGroupData', ['id' => $group['id']]);
            $nbTasks++;
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
         * @var $group \MediaMine\Core\Entity\Video\Group
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
            foreach($data['images'] as $image) {
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
        if ($override) {
            $group->exchangeArrayNoEmpty($tunnelData);
        }
        $this->getEntityManager()->persist($group);
    }
} 
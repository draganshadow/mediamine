<?php

namespace MediaMine\CoreBundle\Tunnel\Mapper;

/**
 * @Service("mediamine.mapper.person")
 */
class PersonMapper extends AbstractMapper{

    public function processPersonDataMappingTask($id)
    {
        /**
         * @var $task \MediaMine\Core\Entity\System\Task
         */
        $task = $this->getRepository('System\Task')->find($id);
        if ($task) {
            $persons = $this->getRepository('Common\Person')->findFullBy(
                array(
                    'id' => $task->reference
                ));
            if (count($persons)) {
                /**
                 * @var $person \MediaMine\Core\Entity\Common\Person
                 */
                $person = $persons[0];

                $this->loadGenres();
                $this->loadCountries();

                $settings = $this->getSettingService()->getSetting('tunnel', 'person');
                $this->mapPersonData($person, $settings);
                $this->getEntityManager()->remove($task);
                $this->getEntityManager()->flush();
            }
            $this->getEntityManager()->clear();
        }
    }

    public function mapAllPersonData()
    {
        $tq = $this->getEntityManager()->createQueryBuilder();
        $nbtask = $tq->select('COUNT(Task)')
            ->from('MediaMine\Core\Entity\System\Task', 'Task')
            ->where('Task.groupKey = \'' . self::KEY . '\'')
            ->where('Task.key = \'person-map\'')
            ->getQuery()
            ->getSingleScalarResult();

        if ($nbtask == 0) {
            $params = array();

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('Person')
                ->from('MediaMine\Core\Entity\Common\Person', 'Person');
            $q = $qb->setParameters($params)->getQuery();

            $iterableResult = $q->iterate();

            $this->getTaskService()->createTasksAndJobs(self::KEY, 'person-map', $iterableResult, 'Tunnel', 'processPersonDataMappingTask');
        }
        $this->getEntityManager()->flush();
    }

    protected function mapPersonData($person, $settings)
    {
        $tunnelPersons = $this->getRepository('Tunnel\Person')->findFullBy(array(
            'personRef' => $person->id,
            'addTunnel' => true
        ));

        if (count($tunnelPersons)) {
            $tps = array();
            foreach ($tunnelPersons as $tp) {
                $tps[$tp->tunnel->key] = $tp;
            }
            $override = true;
            foreach ($settings as $t) {
                if (array_key_exists($t, $tps)) {
                    $this->applyPersonTunnelData($tps[$t], $person, $override);
                    $override = false;
                }
            }
        }
    }

    protected function applyPersonTunnelData(\MediaMine\Core\Entity\Tunnel\Person $data, Person $person, $override = false)
    {
        $tunnelData = array(
            'name'      => $data->name,
            'firstName' => $data->firstName,
            'lastName'  => $data->lastName,
            'country'   => $this->getCountry($data->country),
            'summary'   => $data->summary,
            'images'    => $data->images,
            'birthDate' => $data->birthDate,
            'deathDate' => $data->deathDate,
        );
        if ($override) {
            $person->exchangeArrayUpdate($tunnelData);
        }
        $this->getEntityManager()->persist($person);
    }
} 
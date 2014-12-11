<?php
namespace MediaMine\CoreBundle\Tunnel;

use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Message\System\Task;
use MediaMine\CoreBundle\Service\AbstractService;
use MediaMine\CoreBundle\Shared\MongoEntitityManagerAware;

use JMS\DiExtraBundle\Annotation\Inject;
abstract class AbstractTunnel extends AbstractService
{
    use MongoEntitityManagerAware;

    protected $options = array();

    const SEP = ';';
    protected $statusList = array('new', 'modified');
    protected $rolesList;
    protected $typesList;
    protected $genres = array();
    protected $mode;

    protected $es;


    public function loadOptions() {
        $results = $this->getRepository('System\Setting')->findFullBy([
            'groupKey' => $this->getTunnelName(),
            'hydrate' => Query::HYDRATE_ARRAY
        ]);
        foreach ($results as $result) {
            $this->options[$result['key']] = $result['value'];
        }
    }

    protected function createTasksPool($key, $iterableResult, $service, $randDelay = false)
    {
        /**
         * @var $taskService \MediaMine\CoreBundle\Service\TaskService
         */
        $taskService = $this->getServiceLocator()->get('Task');
        $pool = $taskService->createTaskPool($this->getTunnelName(), $key);
        $i = 0;
        $delay = 0;
        $tasks = array();
        while (($row = $iterableResult->next()) !== false) {
            $item = $row[0];
            $task = new Task();
            $task->exchangeArray(array(
                'groupKey' => $this->getTunnelName(),
                'key' => $key,
                'reference' => $item->id,
                'taskPool' => $pool
            ));
            $this->getEntityManager()->persist($task);
            $tasks[] = $task;


            if ($i % 500 == 0) {
                $this->getEntityManager()->flush();
                foreach ($tasks as $t) {
                    if ($randDelay) {
                        $delay += rand(30,$randDelay);
                    }
                    /**
                     * @var $job \MediaMine\CoreBundle\Job\ServiceCallJob
                     */
                    $job = $this->jobPluginManager->get('MediaMine\CoreBundle\Job\ServiceCallJob');
                    $job->setContent(array(
                        'service' => $service,
                        'method' => 'processTask',
                        'arguments' => $t->id
                    ));
                    $this->queue->push($job, $delay ? array('delay' => $delay): array());
                }
                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear('MediaMine\CoreBundle\Entity\System\Task');
                $tasks = array();
            }
            $i++;
        }
        if (count($tasks)) {
            $this->getEntityManager()->flush();
            foreach ($tasks as $t) {
                if ($randDelay) {
                    $delay += rand(30,$randDelay);
                }
                /**
                 * @var $job \MediaMine\CoreBundle\Job\ServiceCallJob
                 */
                $job = $this->jobPluginManager->get('MediaMine\CoreBundle\Job\ServiceCallJob');
                $job->setContent(array(
                    'service' => $service,
                    'method' => 'processTask',
                    'arguments' => $t->id
                ));
                $this->queue->push($job, $delay ? array('delay' => $delay): array());
            }
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear('MediaMine\CoreBundle\Entity\System\Task');
            $tasks = array();
        }
        $pool->nbTasks = $i;
        $pool->nbRemainingTasks = $i;
        $this->getEntityManager()->persist($pool);
        $this->getEntityManager()->flush();
        $taskService->createTaskPoolCheckJob($pool, array('delay' => 60));
    }


    public function createJobs($tasks, $service, $delay)
    {
        foreach ($tasks as $t) {
            /**
             * @var $job \MediaMine\CoreBundle\Job\ServiceCallJob
             */
            $job = $this->jobPluginManager->get('MediaMine\CoreBundle\Job\ServiceCallJob');
            $job->setContent(array(
                'service' => $service,
                'method' => 'processTask',
                'arguments' => $t->id
            ));
            $this->queue->push($job, $delay ? array('delay' => $delay): array());
        }
    }

    /**
     * Return tunnel name
     * @return string
     */
    abstract function getTunnelName();

    /**
     * Return array of handled entities and fields
     * @return array
     */
    abstract function getAbilities();


    protected function getMode() {
        if (null === $this->mode) {
            $this->mode = 'DEBUG';
        }
        return $this->mode;
    }

    protected function isDebug() {
        return 'DEBUG' === $this->getMode();
    }
}

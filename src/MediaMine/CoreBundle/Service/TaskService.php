<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Job\BaseJob;
use MediaMine\CoreBundle\Message\System\Task;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * @Service("mediamine.service.task")
 */
class TaskService extends AbstractService
{
    /**
     * @Inject("old_sound_rabbit_mq.task_producer")
     * @var Producer
     */
    public $taskProducer;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    public function createTask(Job $job, $service, $method, $parameters) {
        $task = new Task();
        $task->jobId = $job->getId();
        $task->jobService = $job->getService();
        $task->service = $service;
        $task->method = $method;
        $task->parameters = $parameters;

        $this->redis->incr(BaseJob::getNbTasksKey($job->getService(), $job->getId()));
        $this-> execute($task);
    }


    public function execute(Task $task) {
        $this->taskProducer->publish($task->serialize());
    }

}
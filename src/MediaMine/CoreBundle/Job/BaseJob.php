<?php
namespace MediaMine\CoreBundle\Job;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * @Service("mediamine.job.base")
 */
class BaseJob {

    use EntitityManagerAware;
    use ContainerAware;
    use LoggerAware;

    /**
     * @Inject("old_sound_rabbit_mq.job_producer")
     * @var Producer
     */
    public $jobProducer;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;


    public final function canStart($parentJobId = false, $jobId = false) {
        $jobRepository = $this->getRepository('System\Job');

        if ($jobId) {
            /**
             * @var $currentjob Job
             */
            $currentjob = $jobRepository->findFullBy(array(
                'id' => $jobId,
                'hydrate' => Query::HYDRATE_ARRAY,
                'addParentJob' => true,
                'addPreviousJob' => true
            ), true);

            if ($currentjob['previousJob'] && (Job::STATUS_DONE != $currentjob['previousJob']['status'])) {
                return false;
            }
        }

        $jobs = $jobRepository->findFullBy(array(
            'status' => Job::STATUS_RUNNING,
            'hydrate' => Query::HYDRATE_ARRAY,
            'foreignKeys' => true
        ));

        $canStart = true;
        $parent = false;
        $previous = array();

        foreach ($jobs as $job) {
            if ($parentJobId == $job['id']) {
                $parent = $job;
            }
            elseif ($jobId != $job['id'] && $parentJobId == $job['parentjob_id']) {
                $previous[] = $job;
            }
            else {
                /**
                 * @var $otherJob BaseJob
                 */
                $otherJob = $this->container->get($job['service']);
                $canStart = $canStart && $otherJob->allowParallel($this->getServiceName()) && $this->allowParallel($job['service']);
            }
        }
        // test current job allow parallel
        if ($canStart && $parent && count($previous)) {
            /**
             * @var $parentJob BaseJob
             */
            $parentJob = $this->container->get($parent['service']);
            if ($parentJob->allowInnerParallel()) {
                foreach ($previous as $job) {

                    /**
                     * @var $job Job
                     */
                    /**
                     * @var $previousJob BaseJob
                     */
                    $previousJob = $this->container->get($job['service']);
                    $canStart = $canStart && $previousJob->allowParallel($this->getServiceName()) && $this->allowParallel($previousJob['service']);
                }
            } else {
                $canStart = false;
            }
        }
        return $canStart;
    }

    public function allowParallel($jobServiceName) {
        //Base behavior is to prevent same job to run twice in parallel
        return $jobServiceName !== $this->getServiceName();
    }

    public function allowInnerParallel() {
        return false;
    }

    public final function start($groupKey, $key, $params = array(), $jobId = false, $parentJobId = false, $parentJobName = false) {
        $this->logger->info($this->getServiceName() . ': START');
        $jobRepository = $this->getRepository('System\Job');
        /**
         * @var $job Job
         */
        if ($jobId) {
            $job = $jobRepository->findFullBy([
                'id' => $jobId
            ], true);
            $job->setStatus(Job::STATUS_RUNNING);
            $jobRepository->persist($job);
        } else {
            $job = $jobRepository->create([
                'groupKey' => $groupKey,
                'key' => $key,
                'service' => $this->getServiceName(),
                'method' => 'execute',
                'params' => $params,
                'status' => Job::STATUS_RUNNING,
                'nbTasks' => 100,
                'nbTasksDone' => 0
            ]);
        }

        $jobRepository->flush();

        $this->redis->set(self::getStatusKey($this->getServiceName(), $job->getId()), Job::STATUS_RUNNING, $this->getJobTimeout());
        $this->redis->set(self::getNbTasksKey($this->getServiceName(), $job->getId()), 0, $this->getJobTimeout());
        $this->redis->set(self::getNbTasksDoneKey($this->getServiceName(), $job->getId()), 0, $this->getJobTimeout());
        if ($parentJobName && $parentJobId) {
            $this->redis->set(self::getParentJobNameKey($this->getServiceName(), $job->getId()), $parentJobName, $this->getJobTimeout());
            $this->redis->set(self::getParentJobIdKey($this->getServiceName(), $job->getId()), $parentJobId, $this->getJobTimeout());
        }
        return $job;
    }

    public function execute(Job $job) {
        $this->end($job->getId());
    }

    public function createSubJob(Job $parentJob, $key, $service, $parameters = [], $previousJob = null) {
        $jobRepository = $this->getRepository('System\Job');

        /**
         * @var $job Job
         */
        $job = $jobRepository->create([
            'groupKey' => 'sub',
            'key' => $key,
            'service' => $service,
            'method' => 'execute',
            'params' => $parameters,
            'status' => Job::STATUS_NEW,
            'previousJob' => $previousJob,
            'parentJob' => $parentJob,
            'nbTasks' => 100,
            'nbTasksDone' => 0
        ]);

        $jobRepository->flush();
        $nbTasks = $this->redis->incr(BaseJob::getNbTasksKey($this->getServiceName(), $parentJob->getId()));
        $jobMessage = new \MediaMine\CoreBundle\Message\System\Job();
        $jobMessage->service = $service;
        $jobMessage->jobId = $job->getId();
        $jobMessage->parentJobId = $parentJob->getId();
        $jobMessage->parentJobService = $this->getServiceName();
        $jobMessage->groupKey = $this->getServiceName();
        $jobMessage->parameters = $parameters;
        $this->jobProducer->publish($jobMessage->serialize());
        return $job;
    }

    public final function isRunning($jobId) {
        $jobStatus = $this->redis->get(BaseJob::getStatusKey($this->getServiceName(), $jobId));
        return Job::STATUS_RUNNING == $jobStatus;
    }

    public final function taskDone($jobId) {
        $nbTasksDone = $this->redis->incr(BaseJob::getNbTasksDoneKey($this->getServiceName(), $jobId));
        $nbTasks = $this->redis->get(BaseJob::getNbTasksKey($this->getServiceName(), $jobId));
        $seg = (int) ($nbTasks / 10);
        if ($seg == 0) {
            $seg = 1;
        }

        if (($nbTasksDone % $seg) == 0) {
            $jobRepository = $this->getRepository('System\Job');
            /**
             * @var $job Job
             */
            $job = $jobRepository->findFullBy([
                'id' => $jobId
            ], true);
            $job->setNbTasksDone($nbTasksDone);
            $job->setNbTasks($nbTasks);
            $jobRepository->persist($job);
        }

        $this->logger->info($this->getServiceName() . ': ' . $nbTasksDone . '/' . $nbTasks);
        if ($nbTasksDone >= $nbTasks) {
            $this->end($jobId);
        }
    }

    public final function subJobDone($jobId) {
        $this->taskDone($jobId);
    }

    public function end($jobId) {
        $this->logger->info($this->getServiceName() . ': END');
        $jobRepository = $this->getRepository('System\Job');
        /**
         * @var $job Job
         */
        $job = $jobRepository->find($jobId);
        $job->setStatus(Job::STATUS_DONE);
        $nbTasksDone = $this->redis->get(BaseJob::getNbTasksDoneKey($this->getServiceName(), $jobId));
        $nbTasks = $this->redis->get(BaseJob::getNbTasksKey($this->getServiceName(), $jobId));

        if ($nbTasksDone == 0) {
            $nbTasksDone = 1;
        }
        if ($nbTasks == 0) {
            $nbTasks = $nbTasksDone;
        }
        $job->setNbTasksDone($nbTasksDone);
        $job->setNbTasks($nbTasks);

        $jobRepository->persist($job);
        $jobRepository->flush();
        $parentName = $this->redis->get(BaseJob::getParentJobNameKey($this->getServiceName(), $jobId));
        if ($parentName) {
            $parentId = $this->redis->get(BaseJob::getParentJobIdKey($this->getServiceName(), $jobId));
            /**
             * @var $jobService BaseJob
             */
            $parentService = $this->container->get($parentName);
            $parentService->subJobDone($parentId);
        }

        $this->redis->del(self::getStatusKey($this->getServiceName(), $job->id));
        $this->redis->del(self::getNbTasksKey($this->getServiceName(), $job->id));
        $this->redis->del(self::getNbTasksDoneKey($this->getServiceName(), $job->id));
        $this->redis->del(self::getParentJobIdKey($this->getServiceName(), $job->id));
        $this->redis->del(self::getParentJobNameKey($this->getServiceName(), $job->id));
    }

    public function getServiceName() {
        return 'mediamine.job.base';
    }

    public function getJobTimeout() {
        return 86400;
    }

    public static function getStatusKey($name, $id) {
        return $name . ':' . $id . ':status';
    }

    public static function getNbTasksKey($name, $id) {
        return $name . ':' . $id . ':nbTasks';
    }

    public static function getNbTasksDoneKey($name, $id) {
        return $name . ':' . $id . ':nbTasksDone';
    }

    public static function getParentJobIdKey($name, $id) {
        return $name . ':' . $id . ':jobId';
    }

    public static function getParentJobNameKey($name, $id) {
        return $name . ':' . $id . ':jobName';
    }
}
<?php
namespace MediaMine\Service;

use Cron\CronExpression;
use MediaMine\Entity\System\Execution;

class CronService extends AbstractService
{
    public function cron() {
        $this->processExecutions();
    }

    public function execute($id) {
        $criteria = array(
            'id' => $id,
            'status' => Execution::STATUS_PLANNED
        );
        $executions = $this->getRepository('System\Execution')->findBy($criteria, array('scheduleTime' => 'DESC'));
        if (count($executions)) {
            $execution = $executions[0];
            try {
                if (count($execution->service) && count($execution->callback)) {
                    $service = $this->getServiceLocator()->get($execution->service);
                    $method = $execution->callback;
                    if(is_callable(array($service, $method))){
                        $execution->stackTrace = call_user_func(array($service, $method), $execution->arguments);
                    }
                } elseif (count($execution->callback)) {
                    if(is_callable($execution->callback)){
                        $execution->stackTrace = call_user_func($execution->callback, $execution->arguments);
                    }
                }
                $execution->status = Execution::STATUS_DONE;
            } catch (\Exception $e) {
                $execution->errorMsg = $e->getMessage();
                $execution->stackTrace = $e->getTrace();
                $execution->status = Execution::STATUS_ERROR;
            }
            $this->getEntityManager()->persist($execution);
            $this->flush(true);
        }
        return $id;
    }

    public function addCron($key, $frequency, $service, $callback, $arguments) {
        $fields = array();
        CronExpression::factory($frequency);
        $fields['key'] = $key;
        $fields['frequency'] = $frequency;
        $fields['service'] = $service;
        $fields['callback'] = $callback;
        $fields['arguments'] = $arguments;
        $cron = $this->getRepository('System\Cron')->create($fields);
        $this->flush(true);
    }

    /**
     * @param $scheduleTime
     * @param $key
     * @param $service
     * @param $callback
     * @param $arguments
     *
     * @return \MediaMine\Entity\System\Execution
     */
    public function addExecution($scheduleTime, $key, $service, $callback, $arguments) {
        $fields = array();
        $fields['key'] = $key;
        $fields['scheduleTime'] = $scheduleTime;
        $fields['service'] = $service;
        $fields['callback'] = $callback;
        $fields['arguments'] = $arguments;
        $fields['status'] = Execution::STATUS_PLANNED;
        $execution = $this->getRepository('System\Execution')->create($fields);
        $this->flush(true);
    }

    /**
     * @param \MediaMine\Entity\System\Cron $cron
     */
    public function generateCronExecutions(\MediaMine\Entity\System\Cron $cron, $startDate = 'now', $endDate = 'next'){
        $endDate = $endDate !== 'next' ? $endDate : $cron->getNextExecutionDate($startDate);
        $scheduleTime = $cron->getNextExecutionDate($startDate);
        $i = 1;
        while ($endDate >= $scheduleTime) {
            $this->addExecution($scheduleTime, $cron->key, $cron->service, $cron->callback, $cron->arguments);
            $scheduleTime = $cron->getNextExecutionDate($startDate, $i);
        }
    }

    public function processExecutions() {
        $crons = $this->getRepository('System\Cron')->findAll();
        $this->getEntityManager()->clear();
        $now = new \DateTime();
        foreach ($crons as $cron) {
            $criteria = array(
                'key' => $cron->key,
                'status' => Execution::STATUS_PLANNED
            );
            $executions = $this->getRepository('System\Execution')->findBy($criteria, array('scheduleTime' => 'DESC'));
            if (count($executions)) {
                foreach ($executions as $execution) {
                    if ($execution->scheduleTime <= $now) {
                        system($this->getExecuteCommand($execution->id));
                        $execution->status = Execution::STATUS_RUNNING;
                        $this->getEntityManager()->persist($execution);
                    }
                }
            } else {
                $this->generateCronExecutions($cron);
            }
        }
        $this->flush(true);
    }

    public function getNextRootCronExecution() {
        //TODO improve
        $cronExpression = CronExpression::factory($this->getRootCronExpression());
        return $cronExpression->getNextRunDate();
    }

    public function getRootCronExpression() {
        return '*/5 * * * *';
    }

    public function getRootDirectory() {
        return realpath(__DIR__ . '/../../../../../') . '/';
    }

    public function getExecuteCommand($id) {
        return '(php ' . $this->getRootDirectory() . 'public/index.php execute ' . $id . ') >/dev/null 2>/dev/null &';
    }
}
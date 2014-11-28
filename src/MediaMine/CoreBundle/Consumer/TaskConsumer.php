<?php
namespace MediaMine\CoreBundle\Consumer;

use Doctrine\ORM\ORMException;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Entity\System\Job;
use MediaMine\CoreBundle\Job\BaseJob;
use MediaMine\CoreBundle\Message\System\Task;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\Container;
/**
 * @Service("mediamine.consumer.task")
 * @Tag("monolog.logger", attributes = {"channel" = "TaskConsumer"})
 */
class TaskConsumer implements ConsumerInterface
{
    use LoggerAware;

    use ContainerAware;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    public function execute(AMQPMessage $msg)
    {
        try {
            $task = new Task();
            $task->exchangeAMQP($msg);
            if ($task->jobId) {
                /**
                 * @var $jobService BaseJob
                 */
                $jobService = $this->container->get($task->jobService);
                if ($jobService->isRunning($task->jobId)) {
                    $service = $this->container->get($task->service);
                    try {
                        call_user_func(array($service, $task->method), $task->parameters);
                    }
                    catch (ORMException $e) {
                        $this->getLogger()->error($e->getMessage());
                        if ("The EntityManager is closed." == $e->getMessage()) {
                            throw $e;
                        }
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage() . $e->getTraceAsString());
                    }
                    $jobService->taskDone($task->jobId);
                }
            } else {
                $service = $this->container->get($task->service);
                call_user_func(array($service, $task->method), $task->parameters);
            }
        }
        catch (ORMException $e) {
            $this->getLogger()->error($e->getMessage());
            if ("The EntityManager is closed." == $e->getMessage()) {
                throw $e;
            }
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage() . $e->getTraceAsString());
        }
    }
}
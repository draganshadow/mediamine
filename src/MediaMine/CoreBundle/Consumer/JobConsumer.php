<?php
namespace MediaMine\CoreBundle\Consumer;

use Doctrine\ORM\ORMException;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use MediaMine\CoreBundle\Job\BaseJob;
use MediaMine\CoreBundle\Message\System\Job;
use MediaMine\CoreBundle\Shared\ContainerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\Container;

/**
 * @Service("mediamine.consumer.job")
 * @Tag("monolog.logger", attributes = {"channel" = "JobConsumer"})
 */
class JobConsumer implements ConsumerInterface
{
    use LoggerAware;
    use ContainerAware;

    public function execute(AMQPMessage $msg)
    {
        try {
            $jobMsg = new Job();
            $jobMsg->exchangeAMQP($msg);
            $this->getLogger()->debug('Job ' . $jobMsg->service);

            /**
             * @var $service BaseJob
             */
            $service = $this->container->get($jobMsg->service);
            if ($service->canStart($jobMsg->parentJobId, $jobMsg->jobId)) {
                $this->getLogger()->debug('Job ' . $jobMsg->service . ' start');
                $job = $service->start($jobMsg->groupKey, $jobMsg->key, $jobMsg->parameters, $jobMsg->jobId, $jobMsg->parentJobId, $jobMsg->parentJobService);

                if ($jobMsg->parentJobId) {
                    /**
                     * @var $jobService BaseJob
                     */
                    $parentService = $this->container->get($jobMsg->parentJobService);
                    if ($parentService->isRunning($jobMsg->parentJobId)) {
                        $service->execute($job);
                    } else {
                        $service->end($jobMsg->jobId);
                    }
                } else {
                    $service->execute($job);
                }
            } else {
                sleep(30);
                return false;
            }
        }
        catch (ORMException $e) {
            $this->getLogger()->error($e->getMessage());
            if ("The EntityManager is closed." == $e->getMessage()) {
                throw $e;
            }
        }
        catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}
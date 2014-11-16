<?php
namespace MediaMine\CoreBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use SlmQueueDoctrine\Queue\DoctrineQueueInterface;

/**
 * @Service("mediamine.service.worker")
 */
class WorkerService extends AbstractService
{

}

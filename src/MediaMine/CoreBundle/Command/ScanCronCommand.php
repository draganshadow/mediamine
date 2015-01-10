<?php
namespace MediaMine\CoreBundle\Command;

use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Message\System\Job;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \ColourStream\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class ScanCron
 * @package MediaMine\CoreBundle\Cron
 * @CronJob("P1D")
 */
class ScanCronCommand extends ContainerAwareCommand {

    /**
     * @Inject("%mediamine%")
     */
    public $mediamine;

    /**
     * @Inject("old_sound_rabbit_mq.job_producer")
     * @var Producer
     */
    public $jobProducer;


    public function configure()
    {
        $this
            ->setName('mediamine:cron:scan')
            ->setDescription('Scan for new movies')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $this->mediamine['actions']['scan'];
        $job = new Job();
        $job->service = $action['service'];
        $job->groupKey = 'admin';
        $job->key = 'cron.scan';
        $job->parameters = [];
        $this->jobProducer->publish($job->serialize());
        $output->write('ok');
        return true;
    }
} 
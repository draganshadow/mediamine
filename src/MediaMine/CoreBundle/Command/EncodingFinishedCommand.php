<?php
namespace MediaMine\CoreBundle\Command;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EncodingFinishedCommand extends ContainerAwareCommand
{

    /**
     * @var \Redis
     */
    public $redis;

    protected function configure()
    {
        $this
            ->setName('stream:end')
            ->setDescription('Ack the end of a file encoding')
            ->addArgument('key', InputArgument::REQUIRED, 'The pathKey')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument('key');
        $this->redis = $this->getContainer()->get("snc_redis.default");
        $this->redis->hDel('stream', $key);
    }
}